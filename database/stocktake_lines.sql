michposDROP TABLE IF EXISTS mpesa_c2b_payments;
DROP TABLE IF EXISTS mpesa_transactions;
DROP TABLE IF EXISTS mpesa_settings;
DELETE FROM migrations WHERE migration LIKE '%mpesa%';