# IT-Asset-Management-System

#### File Directory Structure
- config
  - config
    system environment settings, base directories, and Database credentials
- public 
  frontend. accessible via browser.
  - css
  - partials
    modular php includes
  - script
    javascripts for dynamic html
  - views
    html/php
- src
  backend. inaccessible via browser.
  - handlers
    handles web requests from frontend
  - manager
    manages repos
  - model
    pure data objects or entities. independent.
  - repos
    low-level repositories for database query interfacing and access. depends on model.
  - utilities
- test 
