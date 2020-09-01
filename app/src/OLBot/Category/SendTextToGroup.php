<?php

namespace OLBot\Category;


use OLBot\Model\DB\AllowedGroup;
use OLBot\Service\MessageService;
use OLBotSettings\Model\SendTextToGroup as SendTextToGroupSettings;

class SendTextToGroup extends AbstractCategory
{
    public function __construct(int $categoryNumber, ?int $subjectCandidateIndex, SendTextToGroupSettings $settings, $categoryhits = [])
    {
        $this->needsSubject = true;
        parent::__construct($categoryNumber, $subjectCandidateIndex, $settings, $categoryhits);
    }

    /**
     * @throws \Exception
     */
    public function generateResponse()
    {
        if (!self::$storageService->botmaster) return;

        $text = $this->getText();
        $group = $this->getGroup();

        $service = new MessageService(self::$storageService->settings->getToken());

        $service->sendMessage($text, $group, self::$storageService->message->getMessageId(), false);
    }

    /**
     * @return mixed
     * @throws \Exception
     */
    private function getGroup()
    {
        foreach (self::$storageService->subjectCandidates as $index => $candidate) {
            if ($index == $this->subjectIndex) continue;
            if (preg_match('#\w+#', strtolower($candidate->text))) {
                $name = strtolower($candidate->text);

                $find = AllowedGroup::where(['name' => $name]);
                if ($find) {
                    return $find->first()->id;
                }
            }
        }
        throw new \Exception('no group found.');
    }
}