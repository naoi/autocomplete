Autocomplete Demo for GCP Storage Services
==========================================

Overview
--------
- This is a [Drupal 8](https://drupal.org/) module for autocomplete demonstration powered by GCP (Google Cloud Platform) using Cloud SQL, Cloud Datastore and BigQuery.

Sample Data
-----------
- This module is designed to use the sample data by [BestBuyAPIs/open-data-set](https://github.com/BestBuyAPIs/open-data-set)

Generate Data
-------------

- **Access**
  - `/admin/content/product_entity/generator`
- **Options**
  - `The offset of start record` and
  - `The number of data to generate`
  - `Create from new data`: If checked, it'll generate new data onto the specified data storage service. If unchecked, it'll copy the existing data from Cloud SQL.
  
Supported GCP Storage Services
-------------------------------

**Cloud Functions**

- Cloud SQL (thru SQL)
- Cloud Datastore (thru Datastore API)
- Need to use your own Redis server

**Cloud LAMP**

- Cloud SQL (thru Drupal's Entity Autcomplete)
- Cloud SQL (thru Druapl's Custom Autocomplete API)
- Cloud Datastore (thru Datastore API)
- BigQuery (thru GQL)

Configuration
-------------

URL path: /admin/config/services/autocomplete

**GCP**

- GCP Keyfile Full Path and Filename (e.g `/home/yourname/<YOUR_PROJECT-ID>.json`)
- Cloud Datastore Project ID
- Cloud Datastore Kind.
- BigQuery Dataset
- BigQuery Table
- Cloud Functions Trigger Cloud SQL Endpoint URL
- Cloud Functions Trigger Cloud SQL Endpoint URL.

**DATABASE**

- Autocomplete Result Max Count
- Products Create Record Total Count
- Poduct Data Count Limit
- Projects JSON URL
