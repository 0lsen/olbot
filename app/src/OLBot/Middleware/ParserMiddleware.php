<?php

namespace OLBot\Middleware;


use OLBot\Category\AbstractCategory;
use OLBot\Model\CategoryHits;
use OLBot\Model\DB\Keyword;
use OLBot\Model\SubjectCandidate;
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

        // TODO: foreach over all subject candidates and find best result (by best subject candidate? by best category result?)
        $text = $this->removeSubjectCandidate(1, $textCopy);
        $categoryHits = $this->getCategoryHits($text);

        foreach ($categoryHits as $categoryHit) {
            if ($categoryHit->hits && $categoryHit->category) {
                $className = '\OLBot\Category\\'.$categoryHit->category;
                /** @var AbstractCategory $cat */
                $cat = new $className($categoryHit->id, 0, $categoryHit->settings, $categoryHits);
                if ($cat->requirementsMet) {
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
        foreach ($this->storageService->settings->parser->quotationMarks as $start => $end) {
            $regexPattern = '#(?:{(?<i>\d+)}|(?<!{\d}))'.preg_quote($start, '#').'([^'.preg_quote($end, '#').']+)'.preg_quote($end, '#').'(?!{/(?P=i)})#';
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

        foreach ($this->storageService->settings->parser->subjectDelimiter as $delimiter) {
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
    }

    private function removeSubjectCandidate($index, $text)
    {
        return preg_replace('#\{'.$index.'}.+{/'.$index.'}#', '', $text);
    }

    /**
     * @param string $text
     * @return CategoryHits[]
     */
    private function getCategoryHits($text)
    {
        $this->cleanUp($text);

        $hits = [];
        foreach ($this->storageService->settings->parser->categories as $id => $category) {
            $hits[$id] = new CategoryHits($id, $category['class'], $category['settings'] ?? []);
        }

        preg_match_all('#\w{3,}#', $text, $words);
        $words = array_unique($words[0]);

        foreach ($words as $word) {
            $keyword = Keyword::find(md5(strtolower($word)));
            if (!is_null($keyword)) {
                if (!isset($hits[$keyword->category])) {
                    $hits[$keyword->category] = new CategoryHits($keyword->category, '', []);
                }
                $hits[$keyword->category]->hits++;
            }
        }

        usort($hits, ['OLBot\Model\CategoryHits', 'cmp']);

        return $hits;
    }

    private function cleanUp(&$text)
    {
        foreach ($this->storageService->settings->parser->stringReplacements as $find => $replace) {
            $text = str_replace($find, $replace, $text);
        }

        $text = strtolower($text);
    }
}