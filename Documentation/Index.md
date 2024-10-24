# Form Double Opt-In

TYPO3 Extension adding Double Opt-In to the TYPO3 CMS Form Framework

## Installation

Just install it as a TYPO3 Extension or with `composer require linawolf/form_double_opt_in`.

Then make sure to include the static TypoScript in your template.

## Configuration

It is recommended to set the default storagePid to a folder where your Opt-In records should be saved to. This can be done in the constants editor of the template or by setting `plugin.tx_formdoubleoptin_doubleoptin.persistence.storagePid` in your TypoScript setup.

## Usage

The Double Opt-In process consists of two parts:

### 1. Double Opt-In Form Finisher

Build your form as usual using the TYPO3 Form Framework backend module. Then add the "Double Opt-In" form finisher and map your fields to the desired fields of the created Opt-In records. Also enter a Page ID where the validation plugin (see below) will be placed.

Available fields in the Opt-In model are:
 + Title
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

If you want an e-mail to be sent to a certain address after the validation set it up in TypoScript constants.

That's it.

### 3. Data Privacy

You can set up a scheduler to delete all entries in the opt-in table older then a certain age. This includes entries
where the user failed to opt in to their mail:

Goto `System > Scheduler` (admins only) and create a new `Scheduled tasks` of Class `Table garbage collection`. Chose a
frequency to run the task repetetly and set up a cron job if you have none yet. Then choose for `Table to clean up`:
`tx_formdoubleoptin_domain_model_optin` and the desired interval. Please note that users who received the email to
double opt-in cannot opt in anymore after there opt-in record has been deleted.

More about using the scheduler in the
`Manual of the system extension scheduler <https://docs.typo3.org/c/typo3/cms-scheduler/main/en-us/>`__.

It is recommended to delete all Opt-In records right after successful opt-in. For this the following TypoScript constant
can be used (enabled by default):

```
plugin.tx_formdoubleoptin_doubleoptin {
    settings {
        deleteOptInRecordsAfterOptIn = 1
    }
}
```

Additionally, it is recommended to run the following scheduler tasks on a regular basis:

 + recycler: Remove deleted records from database. Select Table Op-In (tx_formdoubleoptin_domain_model_optin).
 + scheduler: Table garbage collection. Select table tx_formdoubleoptin_domain_model_optin

Upon deleting records TYPO3 only sets a flag to delete the record. Therefore records concerning data privacy should
be deleted hard from the database on a regular basis. Use the recycler task for this.

When a use registers with your form but does not use the double opt-in their data would stay in the database perpetually.
Use the table garbage collection to delete all records older then 30 days (or set another date limit).

## 4. Attribution

This extension is based on `medienreaktor/form_double_opt_in` by Daniel Kestler.

##  Contribution

### Run tests

```
make install
make test
```

### Fix Rector and CGL

```
make install
make fix
```


### Update PHPstan baseline

If you fixed a phpstan warning you can rebuild the baseline with the following command:

```
make install
make phpstanBaseline
```
