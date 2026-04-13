# Components of an Application Workflow

## CiviCRM

The `reimbursement` extension uses many core CiviCRM features. 

### Case Types

- defined in `/civicrm/a/#/caseType`
- all available *case types* can be used by the extension
- each *case type* might represent a different type of `reimbursement` application
- for each *case type*, individual [configuration options](configuration.md) are available in the extension settings

### Custom Groups and Fields

- defined in `/civicrm/admin/custom/group`
- a *custom group* can be attached to `Case` entities
- each *case type* can have its own set of custom-fields
- the extension adds the public *custom fields* of a case to the form specification

### Case States

- defined in `/civicrm/admin/options/case_status`
- *case status* items can be created depending on the application workflow
  - e.g. `new`, `edit`, `in progress`, `accepted`, `declined`, etc
- all existing *statuses* can be accessed in the extension settings
  - a particular *case-status* can be assigned to cases that are created or finally submitted 

### Expense Types

- defined in `/civicrm/admin/options?reset=1` => *Expense Type*
- *expense types* represent a family of costs, that is, a group where particular cost items can be booked onto 

### Expense States

- defined in `/civicrm/admin/options?reset=1` => *Expense Status*
- usually the predefined status values are good to go
- all existing *expense statuses* can be used in the extension settings
  - a particular *expense status* can be assigned to a newly created expenses

## Drupal

### CMRF

In order to access the *remote profile* provided by the `reimbursement` extension in Drupal, configure `CMRF` as follows:

**CiviMRF Views Dataset**

| Settings                  | Value                      |
|---------------------------|----------------------------|
| CiviMRF Views Dataset     | RemoteCase - Reimbursement |
| CiviMRF-Connector         | CiviRemote                 |
| Entitiy                   | RemoteCase                 |
| Action                    | get                        |
| *Getcount api action*     | *getCount (only necessary for APIv3)* |
| Getfields api action      | getFields                  |
| API Parameters            | `{"remoteContactId":"[current-user:civiremote-id]","profile":"reimbursement"}` |
| Api version               | 4                          |

Note:

- `reimbursement` is the *remote profile* name
- `current-user:civiremote-id` as placeholder token for inserting a user's `Remote Contact ID` when a request is being made

**CMRF Connector**

| Settings                  | Value                      |
|---------------------------|----------------------------|
| CMRF Connector            | `CiviRemote`               |
| Connection Module         | `civiremote`               |
| Profile                   | e.g. `default`              |

Note: this connector should already be available once `CMRF` has been installed.

**CMRF Profile**

| Settings                  | Value                      |
|---------------------------|----------------------------|
| CMRF Connector            | e.g. `default`             |
| URL APIv3                 | `<civicrm api3 url>`       |
| URL APIv4                 | `<civicrm api4 url>`       |
| Sitekey                   | `<civicrm site-key>`       |
| Api-Key                   | `<civicrm api-key>`        |

Note: this profile should already be available once `CMRF` has been installed but all URLs and keys of your particular CiviCRM site must be inserted.

### Frontend Forms in Drupal 

When using Drupal as frontend (with properly configured `CMRF` and `civiremote`), *create* and *update forms* can be visited at particular paths.

**Create Form**

When having configured just a single *case type* in the `reimbursement` extension

* `/civiremote/case/add/reimbursement`

If there are multiple configured *case types* a type selection form is shown at this path.
Each *case type* then has its own path. This path can be used to skip the type selection:

* `/civiremote/case/add/reimbursement?type=<case-type>`

**Update Form**

* `/civiremote/case/<case-id>/update/reimbursement`

Note: The `<case id>` is the ID of an existing `Case` in CiviCRM that must have been created by the currently logged in Drupal user.

### Views
        
One can create `Drupal Views` that display all `Case` entities of the currently logged in user:

- all cases that a user has been created can be accessed
- all Fields of a `Case` can be accessed
- in a View one can filter the list of received `Case` entities by particular fields, e.g. *Case Status* or *Case Subject*
- one can also use context filter in order to select a single `Case` entity by its ID

See the [RemoteCase](features.md/#remotecase) chapter in the [Features](features.md) section.
