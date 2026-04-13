# Installation

## General Requirements

- PHP 8.1+
- CiviCRM 6.5+

## CiviCRM Dependencies

- [de.systopia.remotetools](https://github.com/systopia/de.systopia.remotetools)
- [remote_case](https://github.com/systopia/remote_case)

## Drupal Dependencies

If this extension should talk to a Drupal frontend via `CMRF`, then the following Drupal Modules and their dependencies should be installed:

- [civiremote](https://github.com/systopia/civiremote)
    - latest Release from `1.1.x` Branch
- `civiremote_case`
    - is already contained as module in `civiremote` and must only be enabled
    - this module exposes the `RemoteCase` CiviCRM entity provided by the `remote_case` Extension
    - to be used when configuring up a [CMRF Profile](components.md/#cmrf)

- [cmrf_core](https://github.com/CiviMRF/cmrf_core)

## Installation

### CiviCRM

Use the standard approach for installing CiviCRM extensions as described in the [CiviCRM Sysadmin Guide](https://docs.civicrm.org/sysadmin/en/latest/customize/extensions).

### Drupal

Use the installation instructions that can be found at the pages of the Drupal module dependencies.
