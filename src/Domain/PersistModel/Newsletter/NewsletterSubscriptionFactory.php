<?php

namespace Storytale\CustomerActivity\Domain\PersistModel\Newsletter;

use Storytale\CustomerActivity\Domain\PersistModel\Customer\Customer;

class NewsletterSubscriptionFactory
{
    /**
     * @param string $email
     * @param string $type
     * @param Customer|null $customer
     * @return NewsletterSubscription
     */
    public function build(string $email, string $type, ?Customer $customer = null): NewsletterSubscription
    {
        $uuid = substr(str_shuffle(md5(time())), 0, 16);

        return new NewsletterSubscription($email, true, $type, $uuid, $customer);
    }
}