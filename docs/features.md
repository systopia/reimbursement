# Features

## SYSTOPIA Remote Framework

This extension uses the (SYSTOPIA Remote Tools)[https://github.com/systopia/de.systopia.remotetools] and can be deployed in conjunction with a frontend that supports the SYSTOPIA Remote Tools and JSON Forms (ie. Drupal as shown below)

It implements a *remote profile* that provides two [JSON Forms specifications](https://jsonforms.io) for constructing frontend forms.

It receives the data from the frontend forms via the remote tools and corresponding CiviCRM API calls.

## Remote Profile and Form Specifications

The *specification* of the *remote form* is exposed via a *remote profile*:

- Remote Profile Id: `reimbursement`
- Remote entity: `RemoteCase`
- CiviCRM entity: `Case`

A *JSON Forms specification* maps the fields of a frontend form to the fields of `Case` and `Expense`/`ExpenseLine` entities in CiviCRM. A *specification* contains:

- *form fields* for the core fields of a `Case` entity
- *form fields* for the custom-fields of a `Case` entity  (of a particular *case type*)
- *form fields* for the `ExpenseLines` of a `Case` entity (of particular *expense types*)

A *JSON Forms specification* is provided for two frontend forms:

- *create form* in order to create a new `Case`
- *update form* in order to update an existing `Case`

## Form Data Loading

Existing `Case` data will be requested when loading the *update from*, in order to pre-fill the form:

- a `Remote Contact ID` must be transmitted
- that ID will be resolved to a CiviCRM contact by the *Remote Tools*
- data of the specified case can only be loaded, if the case has been created by a contact with that `Remote Contact ID`
- without properly matching `Remote Contact ID`, no case data can be obtained

## Form Data Submission

Data from the *frontend forms* is being submitted via the Remote-Framework and corresponding CiviCRM API calls:

- submitted data from the *create form* triggers the creation of a new `Case` in CiviCRM
  - new `ExpenseLines` will be attached to that case
- submitted data from the *udpate form* triggers the update of an existing `Case` in CiviCRM
  - existing `ExpenseLines` can be updated or deleted, new `ExpenseLines` can be attached to that case

When creating a new `Case` through the *create form*:  

- a `Remote Contact ID` must be transmitted
- that ID will be resolved to a CiviCRM contact by the *Remote Tools*
- the newly created case will be associated to a contact, if a contact with that `Remote Contact ID` exists
- without properly matching `Remote Contact ID`, no case can be created

When updating an existing `Case` through the *udpate form*:

- the corresponding `case ID` must be transmitted
- a `Remote Contact ID` must be transmitted
- that ID will be resolved to a CiviCRM contact by the *Remote Tools*
- the specified case can only be updated, if the case has been created by a contact with that `Remote Contact ID`
- without properly matching `Remote Contact Id`, no case can be updated

## RemoteCase

All existing `Case` entities of a particular user in CiviCRM are being exposed to the frontend through the *remote profile* and the `RemoteCase` entity:

Obtain the fields of a `Case` entity:

- through a CiviCRM API4 call to `getFields` action of `RemoteCase` entity which in turn maps to `getFields` action of `Case` entity

Obtain all `Case` items of a particular user

- through a CiviCRM API4 call to `get` action of `RemoteCase` entity which in turn maps to `get` action of `Case` entity

## Additional Notes

### Access Cases via Update Form

- The `reimbursement` extension does not make assumptions of allowed *case statuses* to be assigned to cases.
- If cases with a particular *case status* should not be accessed through the frontend *update form*, one would need to implement additional frontend logic in order to restrict the access.

### Use Custom Fields for Cases in CiviCRM But Do Not Display Them in Create or Update Forms at the Frontend

If *custom fields* of a case should be excluded from being displayed in one of the frontend forms, one can mark the *custom group* the fields belong to as *not public* in CiviCRM.

- select the settings of the *custom group* of a case under `/civicrm/admin/custom/group`
- switch off the switch `Is Public`

### CiviCRM Activities

All changes in *case data* via the *update form* are recorded as *case activities* in CiviCRM.
