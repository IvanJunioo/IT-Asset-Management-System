# IT-Asset-Management-System

#### Installation
Download the bash installer: `install.sh`
In the installer's directory, run the following in Linux terminal with a given .env file. Agree when prompted.
`sudo bash install.sh`

#### Uninstallation
Run the following in Linux terminal with a given .env file
`sudo bash uninstall.sh`

#### Backup Restoration
In the project's root, run the following.
`chmod +x db/restore_backup.sh`
`sudo ./db/restore_backup.sh`

#### File Directory Structure
- config
  - config
    system environment settings, base directories, api credentials, and page access permissions
- public 
  frontend. accessible via browser.
  - css
  - script
    javascripts for dynamic html
  - api
    connects to src handlers
- src
  backend. inaccessible via browser.
  - handlers
    handles web requests from frontend
  - manager
    manages repos
  - model
    pure data objects or entities. independent.
  - partials
    modular php includes    
  - repos
    low-level repositories for database query interfacing and access. depends on model.
  - templates
    reporting formats
  - utilities
  - views
    html or php
- test 
