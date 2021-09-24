CREATE TABLE `enkap_payments`
(
    id                    int unsigned  NOT NULL AUTO_INCREMENT,
    currency              varchar(5)             default 'XAF',
    country_code          varchar(3)             default 'CM',
    customer_name         varchar(200)           DEFAULT NULL,
    description           varchar(200)           DEFAULT NULL,
    email                 varchar(128)           DEFAULT NULL,
    client_ip             varbinary(64) NOT NULL DEFAULT '0.0.0.0',
    user_id               int           NOT NULL DEFAULT '0',
    items                 json,
    merchant_reference_id char(36)      NOT NULL DEFAULT '',
    order_transaction_id  varchar(128)           DEFAULT NULL,
    opt_ref_one           text,
    opt_ref_two           text,
    expiry_date           datetime      NOT NULL DEFAULT '2021-05-20 00:00:00',
    order_date            datetime      NOT NULL DEFAULT '2021-05-20 00:00:00',
    phone_number          int           NOT NULL DEFAULT '0',
    total_amount          decimal(32, 2)         DEFAULT '0.00',
    status                varchar(50)            DEFAULT NULL,
    status_date           datetime      NOT NULL DEFAULT '2021-05-20 00:00:00',
    created_at            timestamp     NOT NULL DEFAULT CURRENT_TIMESTAMP,
    updated_at            timestamp     NOT NULL DEFAULT CURRENT_TIMESTAMP ON UPDATE CURRENT_TIMESTAMP,
    PRIMARY KEY (id),
    KEY index_user_id (user_id),
    UNIQUE KEY index_merchant_reference_id (merchant_reference_id),
    UNIQUE KEY index_order_transaction_id (order_transaction_id)
) ENGINE = InnoDB
  DEFAULT CHARSET = utf8;
