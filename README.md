# Consent Fabrik form plugin

This plugin asks and records user's consent in order to be compliant with the GDPR directive.

## What to use it for?

You can use it for **contacts forms**, asking confirmation to the user filling the form that you can process his personal data according to your terms of services.
This is done through a checkbox to check, followed by a custom text asking for consent. This custom text can include a link to your terms of services or any URL.
Once the file is submitted, the consent is recorded in a table in the Joomla! main database. The record includes:
* the date of the submission
* a reference made of the Fabrik list ID, the Fabrik form ID and the row ID of the record
* a seperate list ID, so you can pre-filter the records in case you have several tables recording personal datas you wish to track consent
* a seperate row ID, so you can join your Fabik list with the consent database to have a full view of the data recorded and their consents
* the text of the label that the user agreed to
* the IP address of the user

## Installation

* Download a .zip file of the plugin and install it as any Joomla! extension.
* Once installed, go to the plugin manager, filter out on fabrik form plugins and enable the plugin
* In your Fabrik form settings, add the plugin and configure it as appropriate

You can see this plugin in action on [this page](https://www.betterweb.fr/contact) of our website.
