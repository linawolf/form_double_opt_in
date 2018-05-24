# Form Double Opt-In

TYPO3 Extension adding Double Opt-In to the TYPO3 CMS Form Framework

## Installation

Just install it as a TYPO3 Extension or with `composer require medienreaktor/form_doubleoptin`.

Then make sure to include the static TypoScript in your template.

## Configuration

It is recommended to set the default storagePid to a folder where your Opt-In records should be saved to. This can be done in the constants editor of the template or by setting `plugin.tx_formdoubleoptin_doubleoptin.persistence.storagePid` in your TypoScript setup.

## Usage

The Double Opt-In process consists of two parts:

### 1. Double Opt-In Form Finisher

Build your form as usual using the TYPO3 Form Framework backend module. Then add the "Double Opt-In" form finisher and map your fields to the desired fields of the created Opt-In records. Also enter a Page ID where the validation plugin (see below) will be placed.

Available fields in the Opt-In model are:
 + Given name
 + Last name
 + Email address
 + Company
 + Customer number

At least the email address or the customer number are mandatory fields.

The user will then get an email with a confirmation link to complete the Double Opt-In process.

### 2. Validation Plugin

The validation plugin will verify your Opt-In records by checking the confirmation link and mark your records as validated.

Just place the "Double Opt-In" plugin on a page you like and make sure to define this Page ID in the form finisher (see above).

That's it.
