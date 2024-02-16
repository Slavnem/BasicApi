# Basic Test Api
## Version: 1.0.0
A simple api shared for simple and tutorial purposes. The code fragment was copied from another user and some minor changes were made to it. Link to the original code: https://github.com/daveh/php-rest-api

## Debug
Debugged on Debian 12 Bookworm x64 with bash terminal and curl

## Terminal Commands
### QUERY -> GET ALL USERS DATA
`curl -i -X GET http://localhost:9999/users`
### QUERY -> GET SELECTED USER DATA
`curl -i -X GET http://localhost:9999/users/1`
### QUERY -> CREATE NEW USER
`curl -i -X POST http://localhost:9999/users --data '{"email":"hello@email.com","username":"hello","nickname":"hi"}'`
### QUERY -> UPDATE SELECTED USER
`curl -i -X PATCH http://localhost:9999/users/1 --data '{"username":"changed"}'`
### QUERY -> DELETE SELECTED USER
`curl -i -X DELETE http://localhost:9999/users/1`