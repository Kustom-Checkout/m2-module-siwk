# Changelog

## 1.1.1 / 2026-08-19

### Breaking changes

* None

### Features / changes

* None

### Fixes

* KUSTOM-89: Updated composer requirements

## 1.1.0 / 2026-06-26

### Breaking changes

* None

### Features / changes

* KUSTOM-93: Loosened up PHP version constraint to allow installing the package on Magento 2.4.9
  and PHP 8.5.

### Fixes

* None

## 1.0.15 / 2026-03-06

  * KUSTOM-63 Compatibility with PKSA-y2cr-5h3j-g3ys Security Advisory

## 1.0.13 / 2025-06-03

  * PPP-2071 Fixed callback url

## 1.0.12 / 2025-05-21

  * PPP-2055 Compatibility with AC 2.4.8 and PHP 8.4

## 1.0.11 / 2025-04-23

  * PPP-1814 Added Interoperability functionality

## 1.0.10 / 2025-04-03

  * PPP-1832 Fixed locale
  * PPP-1860 Simplified repository classes for database abstractions

## 1.0.9 / 2025-03-27

  * PPP-2026 Increased version because of new dependencies

## 1.0.8 / 2025-02-11

  * PPP-1983 Increased version because of new dependencies

## 1.0.7 / 2025-01-22

  * PPP-1760 Added API test for Klarna\Siwk\Model\Authentication\Api\Endpoints\Jwks
  * PPP-1761 Added API test for \Klarna\Siwk\Model\Authentication\Api\Endpoints\OpenIdConfiguration
  * PPP-1859 Simplified unit tests by using a helper which includes the mocking logic.

## 1.0.6 / 2024-12-03

  * PPP-1913 Setting the DOB to the customer account if the DOB exists

## 1.0.5 / 2024-11-05

  * PPP-1784 Fix conflicting loads of Klarna web SDKs
  * PPP-1830 List required and default activated scopes for Sign in with Klarna

## 1.0.4 / 2024-10-18

  * PPP-1714 Simplify composer.json files
  * PPP-1726 SIWK - update scope from payment:request:create to customer:login

## 1.0.3 / 2024-09-26

  * PPP-1521 Using the store instance to fetch the locale

## 1.0.2 / 2024-08-30

  * PPP-1665 Changed in the database table klarna_siwk_customer the type of the column klarna_customer_id from text to varchar so that it can be indexed
  * PPP-1671 Checking if the access token is an empty string

## 1.0.1 / 2024-08-21

  * PPP-1652 Updated the version because of new version dependencies

## 1.0.0 / 2024-08-12

  * PPP-754 Added Sign-in with Klarna
