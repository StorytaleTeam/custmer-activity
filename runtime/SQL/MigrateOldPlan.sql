insert into products (created_date, price, doctrine_discr, old_id, name)
VALUES
('2019-11-14 11:26:00', 19, 'subscription_plan_product', 346, 'Individual Monthly'),
('2019-11-15 14:53:00', 48, 'subscription_plan_product', 365, 'Team Monthly'),
('2020-04-17 15:54:00', 480, 'subscription_plan_product', 4621, 'Team Annually'),
('2020-04-17 15:50:00', 192, 'subscription_plan_product', 4622, 'Individual Annually'),
('2020-07-09 17:10:00', 28, 'subscription_plan_product', 13381, 'Individual: Monthly'),
('2020-07-09 17:14:00', 280, 'subscription_plan_product', 13384, 'Individual: Annually'),
('2020-07-09 17:20:00', 48, 'subscription_plan_product', 13386, 'Team: Monthly'),
('2020-07-09 17:25:00', 480, 'subscription_plan_product', 13387, 'Team: Annually');

insert into subscription_plans (id, duration_count, duration_label, download_limit, status, paddle_id)
VALUES
((select id from products where name='Individual Monthly'),1, 'month', 30, 4, 590488),
((select id from products where name='Team Monthly'),1, 'month', 90, 4, 590494),
((select id from products where name='Team Annually'),12, 'month', 90, 4, 590495),
((select id from products where name='Individual Annually'),12, 'month', 30, 4, 590493),
((select id from products where name='Individual: Monthly'),1, 'month', 60, 4, 599038),
((select id from products where name='Individual: Annually'),12, 'month', 60, 4, 599039),
((select id from products where name='Team: Monthly'),1, 'month', 180, 4, 599040),
((select id from products where name='Team: Annually'),12, 'month', 180, 4, 599041);