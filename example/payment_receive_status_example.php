<?php
declare(strict_types=1);

/**
 * Receive Payment status with Automatic notification Url
 * CAMOO SARL: https://www.camoo.cm
 * @copyright (Camoo SARL) camoo.cm
 * @license GPL-2.0-or-later
 * File: payment_receive_status_example.php
 * Created by: Camoo Sarl (e-commerce@camoo.sarl)
 * Description: ENKAP SDK
 *
 * @link http://www.camoo.cm
 */

/**
 * IMPORTANT: It is required that you store Payment order data when submitting Payment as well as the unique id
 *   returned to the response of your Order submission.
 */

//Receive STATUS Information.


$status = $_REQUEST['status'];

$referenceId = \Enkap\OAuth\Lib\Helper::getOderMerchantIdFromUrl();

const BAD_REQUEST = 'HTTP/1.1 400 Bad Request';
//Check if all data was received and return error if any data is missing.

if (empty($status)) {
    header(BAD_REQUEST, true, 400);
    die();
}

/**
 * IMPORTANT: Cross-check data received from Automatic Status Forwarding with the data you stored at Order submission.
 */
/*Check Status data with Order data in your storage. If the unique id has no matching record in the storage
discard Order data.

If there is a match, update record of Order with the new status information.
Store data to persistent storage.

in this example we use PDO connector to a mysql database. In order for this example to work:

i.  $pdo should be the PDO database connector.
ii. Messages should be stored to database table named enkap_payments
iii.    enkap_payments should have the following structure (PS: this definition requires MySQL version 5.7 at least):

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
*/

/*Check if DLR data match with a message sent*/
/** @var PDO $pdo */
$stmt = $pdo->prepare('SELECT * FROM `enkap_payments` WHERE `merchant_reference_id` = ?');
$stmt->execute([$referenceId]);

if ($stmt->rowCount() > 0) {
    /*If YES update the matching record with the DLR data*/
    $row = $stmt->fetch();
    $stmt = $pdo->prepare('UPDATE `enkap_payments` SET `status` = ?, `status_date` = NOW() WHERE `id` = ?');
    if (!$stmt->execute([$status, $row['id']])) {
        /*If update failed, return error*/
        header(BAD_REQUEST, true, 400);
    } else {
        /*If update was successful, return ok*/
        header('HTTP/1.1 200 OK', true, 200);
    }
} else {
    /*If NO matching record for DLR data was found, return error*/
    header(BAD_REQUEST, true, 400);
}
die();
