# IT-Asset-Management-System

#### File Directory Structure
- config
  - config
    system environment settings, base directories, and Database credentials
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
    html/php
- test 

#### Installation
