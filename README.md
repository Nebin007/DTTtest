# DTTtest
This is an assignment for the backend developer intern at DTT. This assignemnt implements the REST API structure on a facility and its tags concepts.

## Route Description
The assignment make use of the [BramUS router](https://github.com/bramus/router) for its routing. The routing information can be found in 
> [routes/routes.php](https://github.com/Nebin007/DTTtest/blob/main/dttsetup/routes/routes.php).
It includes all routes or let's say the links of the assignment.

### CRUD and Search Functionality

In order to perform any of the CRUD or search feature the It should require a token which is in the link Authorization link.
The required Functionality links are(If on localhost):
```
Authorization : http://localhost/dttsetup/auth
Create : http://localhost/dttsetup/create/
Read : http://localhost/dttsetup/read
Update : http://localhost/dttsetup/update/
Delete : http://localhost/dttsetup/delete/
Search : http://localhost/dttsetup/search/
```
The query parameters information etc.. are also shared as postman collection. It can be found [here](https://github.com/Nebin007/DTTtest/blob/main/DTTcollection.postman_collection.json).
In the shared postman collection there is an additional documentation provided for making use of the API.

## API CODE
The API Code is written purely in php. The all functionalities are stored in entity or class called [facility](https://github.com/Nebin007/DTTtest/blob/main/dttsetup/API/facility.php). 
Here I provide the direct links to the code of all the functionalities
>[Authentication](https://github.com/Nebin007/DTTtest/blob/main/dttsetup/API/facility.php#L9)

>[Create](https://github.com/Nebin007/DTTtest/blob/main/dttsetup/API/facility.php#L92)

>[Read](https://github.com/Nebin007/DTTtest/blob/main/dttsetup/API/facility.php#L57)

>[Update](https://github.com/Nebin007/DTTtest/blob/main/dttsetup/API/facility.php#L148)

>[Delete](https://github.com/Nebin007/DTTtest/blob/main/dttsetup/API/facility.php#L197)

>[Search](https://github.com/Nebin007/DTTtest/blob/main/dttsetup/API/facility.php#L208)

Also the configuration of the Database is set up [here](https://github.com/Nebin007/DTTtest/blob/main/dttsetup/config/config.php)

