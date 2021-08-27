<?php

namespace Storytale\CustomerActivity\Application\Query\Newsletter;

interface NewsletterSubscriptionDataProvider
{
    /**
     * @param array $params
     * @return int
     */
    public function count(array $params): int;

    /**
     * @param array $params
     * @return NewsletterSubscriptionBasic[]
     */
    public function getList(array $params): array;
}