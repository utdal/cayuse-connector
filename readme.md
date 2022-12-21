# Description

A single-page web application providing an easy-to-use interface to interact with the [Cayuse](https://cayuse.com) API.

# Features

- Search Cayuse for users, affiliations, roles, trainings, and units
- Bulk upload users, affiliations, trainings, and roles
    - Monitor job status and view job reports of the uploads
    - Option to use a single combined CSV to upload a set of users, their affiliations, & roles (see below for usage)

# Requirements

- Web Server
    - PHP 8.0+
- [Composer](https://getcomposer.org), to install dependencies
- A modern web browser for clients. The front-end JS uses ES6 syntax and [import maps](https://caniuse.com/import-maps).
- A Cayuse installation using Platform (for the searches and training upload) and HR Connect (for the bulk user, affiliation, and role uploads).

# Installation

- Copy (or git pull) these files to a folder on your webserver
- Run `composer install`
- Copy `.env.example` to `.env`
- Edit `.env` with your settings.
    - See the Cayuse support documentation on what server URLs to use for your organization.
    - You should also create an API-specific user in Cayuse and give them the proper roles, e.g. HR Connect Administrator and RS Admin Administrator.
    - List the *exact* names of the roles you want to appear in the role-picker.
- Configure your webserver document root to point to the `public` folder

# Usage notes

## Search String Formats

- Just like the public API, you may use wildcard (*) characters in your search strings

## Standard CSV Formats

- Uploaded CSVs (users, affiliations, roles) should comply with Cayuse HR Connect specified formats for columns, column names, and cell formats
- Uploaded CSVs (trainings) should have the following column titles
    - `Name` (the person's name)
    - `Net ID` (the person's employee id)
    - `Approval Date`
    - `Expires`

## Alternate Combined CSV Format

Columns are filtered prior to upload to only the allowed columns for the type of upload. Also, any required missing columns will be inserted with blank values for all rows. Because of this, you may combine users + affiliations into one combined CSV containing the columns of both, and reuse that file for both the users upload field and the user affiliations upload field. You can also use it for the roles upload field, but must select the `Users only` CSV type and then manually select the roles to add for all of the users in the CSV.

- Example: you can create a single CSV with the following columns, and use it to upload users, affiliations, and (manually selected) roles:
    - `First Name`
    - `Middle Name`
    - `Last Name`
    - `Active`
    - `Employee ID`
    - `Email`
    - `Username`
    - `Create Account`
    - `User Active`
    - `Unit Primary Code`
    - `Unit Name`
    - `Title`
    - `Primary Appointment`

# Contributing

- Open a Github Issue describing in detail the bug, suggestion, feature request, or general issue
- PRs are appreciated: Fork, commit changes, and submit a pull request (PR)

# License and caveats

- This application is designed to be used by technical users and shouldn't be deployed in a public production environment.
- This application is provided "as is" under the MIT license (see LICENSE.md)