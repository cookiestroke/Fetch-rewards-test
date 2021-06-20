# Fetch-rewards-test

You can check out the deployed version at

http://wedosecure.great-site.net/fetchtest

**To install it on your system -**

**Requirements -** Apache, PHP, MySQL

Change the conn.test parameters such that:

<pre>
$conn = mysqli_connect(<i>your_hostname, your_sql_username, your_sql_password, your_database_name</i>);
</pre>

Also create these mysql tables either by importing the SQL file or creating it manually in your database

Table Name - **uwallet**
**windex** | **payer** | **points**
--- | --- | ---
int, Auto Increment, Primary | varchar | int

Table Name - **tsaction**
**tindex** | **payer** | **points**| **tstamp**
--- | --- | ---| ---
int, Auto Increment, Primary | varchar | int| timestamp
