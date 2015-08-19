# Members User Edit Restrictions

Essential restrictions for users editing users when custom roles created with the [Members plugin](https://wordpress.org/plugins/members/) are present.

## Installation

Note that the plugin folder should be named `members-user-edit-restrictions`. This is because if the [GitHub Updater plugin](https://github.com/afragen/github-updater) is used to update this plugin, if the folder is named something other than this, it will get deleted, and the updated plugin folder with a different name will cause the plugin to be silently deactivated.

## Filter hooks

* `pilau_muer_role_hierarchy` - The artificial 'role hierarchy' used to enforce restrictions on what one user can do with user accounts of another role; return a simple array of role names, 'highest' roles first

## Action hooks

