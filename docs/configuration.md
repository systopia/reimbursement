# Configuration

## Case Status Settings

Settings can be found at:

- Navigation Menu -> Administration -> CiviCase -> Reimbursement Case Type Config

or directly at the url:

- `/civicrm/admin/reimbursement/case-type-config`

## Configuration Values

For each *case type* a particular configuration profile can be created.

Each profile affects the way how `Case` data is being send and received to and from a frontend application form and how a frontend form is being rendered.

If profiles for more than one *case type* has been configured, individual frontend forms are available for each *case type*, as explained [when Drupal is being used as frontend](components.md/#frontend-forms-in-drupal).

### Create a New Configuration Profile for a Particular Case Type

Click `Add` on the upper right corner of the settings page.

A new window will open. One can now configure the behavior of the extension for a particular *case type*.

### Case Type

Select the *case type* for this particular configuration profile.

- the drop-down menu will show all available [case types](components.md/#case-types)
- only a single *case type* can be selected

### Initial Case Status

Select the *case status* that a `Case` receives when form data is being transmitted from frontend to backend and a new `Case` is created.

This status will be set when the `Save` button is being pressed in *create form*.

If not set, a new `Case` that is created via the *create form* will receive a default status selected by CiviCRM.

### Submit Case Status

Select the *case status* that a `Case` receives when form data is transmitted from frontend to backend and a new `Case` is created.

This status will be set when the `Submit` button is being pressed in *create* or *update form*.

If this status is not set, then

- The field for adjusting the `Submit Button Label` is not being displayed in the configuration window.
- The `Submit` button itself won't be displayed in the frontend form.
- A new `Case` that is created via the *create form* will always get the initial *case status*.

### Writable Case Status

Select the allowed *case statuses* for this particular *case type*.
One would usually select all available *case-status* values.

### Available Expense Types

Select all *expense types* that should be available for the this particular *case type*.

- The drop-down menu will show all available [expense types](components.md/#expense-types).
- For each selected *expense type*, a section to add expenses of that type will be displayed in the rendered frontend form.

Each section in the rendered frontend form will consist of the following elements:

- Buttons to dynamically add or remove *cost items* of that particular *expense type*.
- Buttons to dynamically add or remove file-attachments for each *cost item*.
- Labels of buttons can be adjusted.

If no *expense types* are selected, no expense section will be displayed in the frontend form.

### Expense Status

Select the status that a `Expense` receives once it has been created and attached to a `Case`.

This is usually one of the predefined [expense statuses](components.md/#expense-states).

### Placement of Expenses

`Above Case Fields` (default)

The Expense section will be placed and rendered in the frontend form above the case fields.

`Below Case Fields`

The Expense section will be placed and rendered in the frontend form below the case fields.

### Save Button Label

The label of the `Save` button in *create* and *update form*.

If not set, a default label will be used.

### Submit Button Label

The label of the `Submit` button in *create* and *update form*.

If not set, a default label will be used.

Note: this option is only displayed in the settings window, if a `Submit Case Status` [has been set](#submit-case-status).

### Expense Add Label

The label of the `Add Expense` button in the expense section of the frontend form.

- This labels affects the `Add Expense` button for all *expense types*.
- If not set, a default label will be used.

Note: if no *expense types* [have been selected](#available-expense-types), none of the expense control buttons will be displayed.

### Expense Remove Label

The label of the `Remove Expense` button in the expense section of the frontend form.

- This labels affects the `Remove Expense` button for all *expense types*.
- If not set, a default label will be used.

Note: if no *expense types* [have been selected](#available-expense-types), none of the expense control buttons will be displayed.

### Attachment Add Label

The label of the `Add Attachment` button in the expense section of the frontend form.

- This labels affects the `Add Attachment` button for all *cost items*.
- If not set, a default label will be used.

Note: if no *expense types* [have been selected](#available-expense-types), none of the expense control buttons will be displayed.

### Attachment Remove Label

The label of the `Remove Attachment` button in the expense section of the frontend form.

- This labels affects the `Delete Attachment` button for all *cost items*.
- If not set, a default label will be used.

Note: if no *expense types* [have been selected](#available-expense-types), none of the expense control buttons will be displayed.

### Subject Field Enabled

If enabled, the `subject` field of a `Case` will be displayed in the frontend form:

- `subject` field is a core `Case` field
- it will be displayed above all *custom fields*
- the label of the field can be overwritten by `Subject Field Label`
- a description of the field can be provided by `Subject Field Description`
- the description is usually displayed as help text in the frontend form

If not enabled, the `subject` field of a `Case` won't be displayed in the frontend form.
  
### Details Field Enabled

If enabled, the `details` field of a `Case` will be displayed in the frontend form:

- `details` field is a core `Case` field
- it will be displayed above all *custom fields*
- the label of the field can be overwritten by `Details Field Label`
- a description of the field can be provided by `Details Field Description`
- the description is usually displayed as help text in the frontend form

If not enabled, the `details` field of a `Case` won't be displayed in the frontend form.

### Start Date Field Enabled

If enabled, the `start_date` field of a `Case` will be displayed in the frontend form:

- `start_date` field is a core `Case` field
- it will be displayed above all *custom fields*
- the label of the field can be overwritten by `Start Date Field Label`
- a description of the field can be provided by `Start Date Field Description`
- the description is usually displayed as help text in the frontend form

If not enabled, the `start_date` field of a `Case` won't be displayed in the frontend form.

### Stop Date Field Enabled

If enabled, the `stop_date` field of a `Case` will be displayed in the frontend form:

- `stop_date` field is a core `Case` field
- it will be displayed above all *custom fields*
- the label of the field can be overwritten by `Stop Date Field Label`
- a description of the field can be provided by `Stop Date Field Description`
- the description is usually displayed as help text in the frontend form

If not enabled, the `stop_date` field of a `Case` won't be displayed in the frontend form.
