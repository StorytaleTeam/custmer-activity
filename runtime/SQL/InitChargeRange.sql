UPDATE customer_activity.public.subscription_plans
SET charge_period_count = customer_activity.public.subscription_plans.duration_count, charge_period_label = customer_activity.public.subscription_plans.duration_label
WHERE customer_activity.public.subscription_plans.paddle_id NOT IN ('599041', '599039', '590493');

UPDATE customer_activity.public.subscription_plans
SET charge_period_count = 12, charge_period_label = customer_activity.public.subscription_plans.duration_label
WHERE customer_activity.public.subscription_plans.paddle_id IN ('599041', '599039', '590493');

-- prod year plans: '599041', '599039', '590493'
-- test year plans: '14889', '14887', '14853', '14851'