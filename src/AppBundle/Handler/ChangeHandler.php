<?php

namespace AppBundle\Handler;

use AppBundle\Entity\Progress;
use Doctrine\ORM\EntityManager;

class ChangeHandler
{
    protected $entityManager;
    protected $nodeUrl;
    protected $requiredMoney;
    protected $hourCost;

    /**
     * ChangeHandler constructor.
     *
     * @param EntityManager $entityManager
     * @param               $nodeUrl
     * @param               $requiredMoney
     * @param               $hourCost
     */
    public function __construct(EntityManager $entityManager, $nodeUrl, $requiredMoney, $hourCost)
    {
        $this->entityManager = $entityManager;
        $this->nodeUrl = $nodeUrl;
        $this->requiredMoney = $requiredMoney;
        $this->hourCost = $hourCost;
    }


    /**
     * Handle the sent data and inform node server
     *
     * @param $data
     *
     * @return string
     */
    public function handle($data)
    {
        if ($this->isHandleable($data)) {
            $attrs = $data['change']['diff']['custom_attributes'];
            $timeSpentTo = $this->convertToSeconds($attrs['to'][0]['value']);
            $timeSpentFrom = $this->convertToSeconds($attrs['from'][0]['value']);

            $this->createProgress($timeSpentFrom, $timeSpentTo);

            $progressSeconds = $this->countProgressForToday();

            $progress = $this->computeProgress($progressSeconds);
            $progressPercent = $this->computeProgressPercent($progress);

            $this->callNode($progress, $progressPercent);
            return "ok";

        }

        return "no change";
    }

    /**
     * Is the sent data is a change ?
     * @param $data
     *
     * @return bool
     */
    protected function isHandleable($data)
    {
        return $data['action'] == 'change'
            && array_key_exists('custom_attributes', $data['change']['diff'])
            && $data['change']['diff']['custom_attributes']['to'][0]['name'] == 'Time spent';

    }

    /**
     * Convert **h **min **sec into a number of seconds
     *
     * @param $time
     *
     * @return int
     */
    protected function convertToSeconds($time)
    {
        $hours = $minutes = $seconds = [];
        preg_match("/(\d+)(?=h)/", $time, $hours);
        preg_match("/(\d+)(?=m)/", $time, $minutes);
        preg_match("/(\d+)(?=s)/", $time, $seconds);

        $timeSpent = 0;
        if (!empty($seconds[1])) {
            $timeSpent += $seconds[1];
        }
        if (!empty($minutes[1])) {
            $timeSpent += $minutes[1] * 60;
        }
        if (!empty($hours[1])) {
            $timeSpent += $hours[1] * 60 * 60;
        }

        return $timeSpent;
    }

    /**
     * Fetch the database to count the number of seconds logged today
     *
     * @return int
     */
    protected function countProgressForToday()
    {
        $from = new \DateTime();
        $from = $from->setTime(0, 0, 0);
        $to = new \DateTime();
        $to = $to->setTime(23, 59, 59);

        $progress = $this->entityManager->getRepository('AppBundle:Progress')->sumForDate($from, $to);

        return $progress[0][1];

    }

    /**
     * Send a request to node server with progress informations
     *
     * @param $progressEuro
     * @param $progressEuroPercent
     */
    protected function callNode($progressEuro, $progressEuroPercent)
    {
        $ch = curl_init();
        curl_setopt($ch, CURLOPT_URL, $this->nodeUrl."?euro=".$progressEuro."&percent=".$progressEuroPercent);
        curl_exec($ch);
        curl_close($ch);
    }

    /**
     * Create a new Progress entity with handled datas
     * @param $timeSpentFrom
     * @param $timeSpentTo
     *
     * @return Progress
     */
    protected function createProgress($timeSpentFrom, $timeSpentTo)
    {
        $progress = new Progress();
        $progress->setRawTo($timeSpentTo);
        $progress->setRawFrom($timeSpentFrom);
        $progress->setSeconds($timeSpentTo - $timeSpentFrom);
        $progress->setCreatedAt(new \DateTime("now"));

        $this->entityManager->persist($progress);
        $this->entityManager->flush();

        return $progress;
    }

    /**
     * Calculate the billable price
     *
     * @param $progressSeconds
     *
     * @return float
     */
    private function computeProgress($progressSeconds)
    {
        $priceForSecond = $this->hourCost/60/60;

        return $priceForSecond * $progressSeconds;
    }

    /**
     * Calculate the progress percentage
     *
     * @param $progress
     *
     * @return float
     */
    private function computeProgressPercent($progress)
    {
        $needsADay = $this->requiredMoney / 20; // ~number of working days

        return $progress / $needsADay;
    }


}
