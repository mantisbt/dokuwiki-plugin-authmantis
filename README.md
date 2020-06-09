# DokuWiki / Mantis Authentication Plugin

Enables single sign-on to DokuWiki, using external Trust mechanism to
authenticate the user against MantisBT's user cookie.

Copyright (c) 2006 Victor Boctor  
Copyright (c) 2007-2012 Victor Boctor, Tiago Gomes and various contributors  
Copyright (c) 2013 Damien Regad


## License

This program is free software: you can redistribute it and/or modify
it under the terms of the 
[GNU General Public License](https://www.gnu.org/licenses/gpl-3.0.html) 
as published by the Free Software Foundation, either version 3 of the
License, or (at your option) any later version.

This program is distributed in the hope that it will be useful,
but WITHOUT ANY WARRANTY; without even the implied warranty of
MERCHANTABILITY or FITNESS FOR A PARTICULAR PURPOSE.  See the
GNU General Public License for more details.


## Compatibility

This plugin has been tested with the following DokuWiki releases:

- 2013-05-10 “Weatherwax”


## Installation and Configuration

Install the plugin following standard
[instructions](https://www.dokuwiki.org/plugin_installation_instructions)
from the DokuWiki website.

Make sure it is installed in `lib/plugins/authmantis/` - if the folder is
called differently, it will not work!

### ACL setup

Setup the Access Control List groups as appropriate for your environment by 
defining entries in `acl.auth.php` for each access level defined in your 
MantisBT instance. 

For example, with default MantisBT access levels your setup could be:
```
# MantisBT access levels
*  @VIEWER         1
*  @REPORTER       2
*  @UPDATER        4
*  @DEVELOPER      8
*  @MANAGER        16
*  @ADMINISTRATOR  16
```

### Set the Authentication Backend

1. Open the DokuWiki _Configuration Manager_
2. In the _Authentication_ section:
   - **Check** _Use access control lists_
   - Select **authmantis** as _Authentication Backend_
   - Enter **@ADMINISTRATOR** as _Superuser_
3. In the _Authmantis_ section:
   - Set the path to your MantisBT installation (i.e. the location of the 
     `core.php` file).  
4. **Save** the changes.

Alternatively, you can manually edit `local.php` or `local.protected.php`:
```
$conf['useacl']      = 1;
$conf['authtype']    = 'authmantis';
$conf['superuser']   = '@ADMINISTRATOR';
$conf['plugin']['authmantis']['mantis_root'] = '/path/to/mantisbt/';
``` 


## Support

Source code and support for this plugin can be found at
https://github.com/mantisbt/dokuwiki-plugin-authmantis


## Credits

This plugin is based on the Authentication back-end written by Victor Boctor,
published in the [MantisBT Wiki] back in 2006. The latest code available in the
wiki ([2012-12-01 13:26]) was used to develop it. Please refer to the page's
revision history for details about earlier authors and changes.

[MantisBT Wiki]: https://mantisbt.org/wiki/doku.php/mantisbt:issue:7075:integration_with_dokuwiki.
[2012-12-01 13:26]: https://mantisbt.org/wiki/doku.php/mantisbt:issue:7075:integration_with_dokuwiki?rev=1354364789
