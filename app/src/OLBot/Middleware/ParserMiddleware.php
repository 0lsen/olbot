<?php

namespace OLBot\Middleware;


use OLBot\Category\AbstractCategory;
use OLBot\Model\CategoryHits;
use OLBot\Model\DB\Keyword;
use OLBot\Model\SubjectCandidate;
use OLBot\Util;
use Slim\Http\Request;
use Slim\Http\Response;

class ParserMiddleware extends TextBasedMiddleware
{
    const OPEN_TAG = '#{\d+}#';
    const CLOSE_TAG = '#{/\d+}#';

    public function __invoke(Request $request, Response $response, $next)
    {
        $textCopy = &$this->storageService->textCopy;

        $this->removeTags($textCopy);

        $this->findSubjectCandidates($textCopy);

        $bestCandidate = $this->chooseBestSubjectCandidate();

        $text = $this->removeSubjectCandidate($bestCandidate+1, $textCopy);

        $categoryHits = $this->getCategoryHits($text);

        foreach ($categoryHits as $categoryHit) {
            if ($categoryHit->getHits() && $categoryHit->getCategory()) {
                $className = '\OLBot\Category\\'.$categoryHit->getCategory();
                /** @var AbstractCategory $cat */
                $cat = new $className($categoryHit->getId(), $bestCandidate, $categoryHit->getSettings(), $categoryHits);
                if ($cat->requirementsMet()) {
                    $cat->generateResponse();
                    $this->storageService->sendResponse = true;
                    break;
                }
            }
        }

        return $next($request, $response);
    }

    private function removeTags(&$text)
    {
        $text = preg_replace([self::OPEN_TAG, self::CLOSE_TAG], ['', ''], $text);
    }

    private function findSubjectCandidates(&$text)
    {
        foreach ($this->storageService->settings->getParser()->getQuotationMarks() as $tuple) {
            $start = $tuple->getKey();
            $end = $tuple->getValue();
            $regexPattern = '#(?:{(?<i>\d+)}|(?<!{\d}))'.preg_quote($start, '#').'([^'.preg_quote($start.$end, '#').']+)'.preg_quote($end, '#').'(?!{/(?P=i)})#';
            preg_match_all($regexPattern, $text, $matches);
            for ($i = 0; $i < sizeof($matches[0]); $i++) {
                $match = $matches[2][$i];
                $this->removeTags($match);
                $this->storageService->subjectCandidates[] = new SubjectCandidate(
                    SubjectCandidate::QUOTATION,
                    $matches[1][$i],
                    $match
                );
                $index = sizeof($this->storageService->subjectCandidates);
                $text = preg_replace(
                    '#'.preg_quote($matches[0][$i], '#').'#',
                    '{'.$index.'}'.$matches[0][$i].'{/'.$index.'}',
                    $text,
                    1
                );
            }
        }
//        // catch Text in Quotation marks that isn't already encapsuled in matching curly brackets
//        $regexPattern = '#(?:{(?<i>\d+)}|(?<!{\d}))((?<q>['.preg_quote($quotationMarks, '#').']).+?(?P=q))(?!{/(?P=i)})#';
//        // limit to 10 because of '(?<!{\d})' - lookbehind fixed width prevents the use of '\d+' here
//        while (preg_match($regexPattern, $text, $match) && sizeof($this->storageService->subjectCandidates) < 10)

        foreach ($this->storageService->settings->getParser()->getSubjectDelimiters() as $delimiter) {
            if (preg_match('#'.preg_quote($delimiter, '#').'(.+)$#', $text, $matches)) {
                $match = $matches[1];
                $this->removeTags($match);
                $this->storageService->subjectCandidates[] = new SubjectCandidate(
                    SubjectCandidate::DELIMITER,
                    $delimiter,
                    $match
                );
                $index = sizeof($this->storageService->subjectCandidates);
                $text = preg_replace(
                    '#'.preg_quote($matches[1], '#').'$#',
                    '{'.$index.'}'.$matches[1].'{/'.$index.'}',
                    $text,
                    1
                );
            }
        }

        foreach ($this->storageService->settings->getParser()->getAuthorHints() as $hint) {
            if (preg_match('#'.preg_quote($hint, '#').'#', $text, $matches)) {
                $this->storageService->authorHint = $matches[1];
            }
        }
    }

    private function chooseBestSubjectCandidate()
    {
        $index = null;
        $words = 0;

        foreach ($this->storageService->subjectCandidates as $i => $candidate) {
            if (is_null($index)) {
                $index = $i;
                $words = preg_match_all('#\w+#', $candidate->text);
            } else {
                $wordsNew = preg_match_all('#\w+#', $candidate->text);
                if ($wordsNew > $words) {
                    $index = $i;
                    $words = $wordsNew;
                }
            }
        }

        return $index;
    }

    private function removeSubjectCandidate($index, $text)
    {
        return preg_replace('#\{'.$index.'}.+{/'.$index.'}#', '', $text);
    }

    /**
     * @param string $text
     * @return CategoryHits[]
     */
    private function getCategoryHits(string $text)
    {
        /** @var CategoryHits[] $hits */
        $hits = [];
        foreach ($this->storageService->settings->getParser()->getCategories() as $category) {
            $hits[$category->getCategoryNumber()] = new CategoryHits($category->getCategoryNumber(), $category->getType(), $category);
        }

        $words = Util::getWords(Util::replace($text, $this->storageService->settings->getParser()->getStringReplacements()));

        foreach ($words as $word) {
            $keyword = Keyword::find(md5(strtolower($word)));
            if (!is_null($keyword)) {
                if (!isset($hits[$keyword->category])) {
                    $hits[$keyword->category] = new CategoryHits($keyword->category, '', null);
                }
                $hits[$keyword->category]->addHit($word);
            }
        }

        usort($hits, ['OLBot\Model\CategoryHits', 'cmp']);

        return $hits;
    }
}