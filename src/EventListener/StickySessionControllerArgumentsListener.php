<?php

namespace App\EventListener;

use Symfony\Component\HttpKernel\Event\ControllerArgumentsEvent;
use Throwable;

class StickySessionControllerArgumentsListener
{
    private const KEY_AB_HYPOTHESIS = 'ab-hypothesis';

//    HINT: this can be injected as app paramater, not as const
    private const MAP_HYPOTHESES_DENSITY = ['a' => 50, 'b' => 25, 'c' => 25];

    /**
     * @var string
     */
    private $abSecret;

    public function __construct(string $abSecret)
    {
        $this->abSecret = $abSecret;
    }

    /**
     * @param ControllerArgumentsEvent $event
     */
    public function onKernelControllerArguments(ControllerArgumentsEvent $event)
    {
        try {
            $controller = $event->getController();
            if (!in_array($controller[1], $controller[0]->getAbTestableActions())) {
                return;
            }

            $currentHypothesis = $event->getRequest()->cookies->get($this->getSessionKey());

            if (!$currentHypothesis) {
                $currentHypothesis = $this->getRandomizedHypothesis();

                $event->getRequest()->cookies->set($this->getSessionKey(), base64_encode($currentHypothesis));
            } else {
                $currentHypothesis = base64_decode($currentHypothesis);
            }

            $arguments = $event->getArguments();
            array_pop($arguments);
            $arguments[] = $currentHypothesis;
            $event->setArguments([$event->getRequest(), $currentHypothesis]);

        } catch (throwable $exception) {
            $this->log($exception->getMessage(), $exception->getTraceAsString());
        } finally {
            return;
        }

    }

    /**
     * @return string
     */
    public function getSessionKey(): string
    {
        return hash('sha256', sprintf('%s~%s', $this->abSecret, self::KEY_AB_HYPOTHESIS));
    }

    /**
     * @return string
     * @throws \Exception
     */
    private function getRandomizedHypothesis(): string
    {
        $randomNumber = random_int(0, 100);
        $distribution = $this->getHypothesesDistribution();

        foreach ($distribution as $hypothesis => $highLimit) {
            if ($randomNumber <= $highLimit) {
                return $hypothesis;
            }

        }
        throw new \Exception(sprintf('Something went wrong with given distribution'));

    }

    /**
     * @return array
     * @throws \Exception
     */
    private function getHypothesesDistribution(): array
    {
        $density = [];
        $carry = 0;

        foreach (self::MAP_HYPOTHESES_DENSITY as $hypothesis => $percentage) {
            if (!is_numeric($percentage) || $percentage <= 0) {
                throw new \Exception(sprintf('Invalid percentage for %s hypothesis, must be a number greater than 0, %s given', $hypothesis, $percentage));
            }

            $density[$hypothesis] = $carry + $percentage;
            $carry += $percentage;
        }

        if ($carry !== 100) {
            throw new \Exception(sprintf('Invalid AB hypothesis density given, sum of percentages must be equal 100, %d obtained', $carry));
        }

        return $density;
    }

    private function log(string $message, string $context): void
    {
//        Logger code goes here
    }
}