# Introduction

This extension helps to manage reimbursements. Reimbursements usually occur while working with applications for funding programs.

It can facilitate the implementation of a reimbursement application workflow in CiviCRM (as backend) and a frontend (through the *SYSTOPIA Remote Framework*).

It assumes a application workflow that is consisting of the following components.

## Application Data as Case Entity

A [`Case`](https://docs.civicrm.org/user/en/latest/case-management/what-is-civicase/) of a particular *case type* can represent a single reimbursement application:

- application types for different needs can be designed by using different *case types*
- for each available *case type*, *custom fields* can be attached to a `Case` entity

## Application Cost Items as Expenses and ExpenseLine Entities

All *costs items* of a application can be represented by `Expenses`:

- a single `Case` can have one or more [`Expenses`](https://docs.civicrm.org/expenses/en/latest/) of different *expense types*
- each *expense type* might represent a particular *cost family*
- each `Expense` can have several `ExpenseLines` that represent single *cost items* within that *cost family*

## Application Form

A form can be used to create and update reimbursement applications:

- the form can show all core and custom fields of a `Case` entity of a particular *case type*
- a form specification resembling all `Case` fields is exposed by this extension via the [SYSTOPIA Remote Framework](https://github.com/systopia/de.systopia.remotetools)
- a form specification can be used to render the form in a frontend, e.g. it can be rendered to a form using the [CiviRemote](https://github.com/systopia/civiremote) Drupal module.
- a filled-out form can be submitted back to CiviCRM/this extension via the *Remote Framework*
