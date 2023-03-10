# CiviETL

An ETL tool for CiviCRM



### Architecture
There are four services one must select for a project:
* Reader (is your data in CSV? SQL? Excel? A web API?)
* Original Cache (do we store loaded data in an array? Redis? SQL?)
* Transformed cache(?)
* Writer (Do we output data to a local CiviCRM via API4? Remote CiviCRM? JSON? CSV?)

