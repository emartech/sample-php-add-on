# Sample PHP integration

This guide is intended to give you an overview on what should be kept in mind when you develop an integration (add-on) to the Emarsys B2CMC.

We will show you an integration that provides embeddable user interface.

## Add-on with UI integration

Add-ons which provide a user interface can be embedded into the Emarsys B2CMC via iframes.

In case you are developing an add-on with UI integration, you will have to provide the URL of the entry point of your interface.

> Please note that until your add-on is approved by Emarsys, we only allow integration to our staging/sandbox environment. Once the application is ready to go live, we'll also configure it on our production servers.
This basically means that you should provide two URLs, one for your staging environment and one for production (which will be visible for the users).

The URL you provide will be embedded into the Emarsys interface, thus it must be consistent with the rest of the B2CMC's UI. Please, use this [UI Kit](https://ui.static.emarsys.net) to meet this requirement.

To embed the add-on, we will [Escher](https://escherauth.io/) pre-sign the URL you provided and load it into an iframe. You must validate the signature (which is appended to the URL via query parameters) and authorize user access only if the check succeeds. ([Here is a code example for this.](/src/SampleIntegration/Middleware/EscherAuthenticationMiddleware.php))

To validate the signature, we will provide the ID of the key which will be used for communication, the secret which will be used to sign the request and the credential scope in which your key is considered to be valid.

> Please note that when communicating with Emarsys you will have to use eu/suite/ems_request as credential scope. When Emarsys calls the add-on, we will use its own scope (e.g.: eu/my-service/ems_request). ([Here is a code example for this.](/src/SampleIntegration/EscherFactory.php)).


## Session handling

If the user can trigger any action through the integrated UI of your add-on, you will have to implement some kind of session handling.

> Please note that the Emarsys B2CMC allows users to log in with multiple customer/administrator accounts simultaneously. These parallel sessions must be tracked individually and they must not interfere with each other.

### JWT

There are several ways of tracking users and their belonging sessions in a web application. As using cookies or persisting information on the server side would have introduced an unnecessary complexity in our application, we chose to implement the above-mentioned behavior using [JWT tokens](https://jwt.io/introduction/).

When the user logs in to the application, a token is generated which is then passed around with each request (page views, redirects and AJAX requests).

In our case, the entry point is the [/login](/src/SampleIntegration/Controller/LoginController.php) page which accepts Escher (pre-)signed GET requests. When the user visits the add-on, we validate the signature to ensure that the user was authenticated by Emarsys and a session may be started.
Once the session token is assembled the user is redirected to the applications [/index](/src/SampleIntegration/Controller/IndexController.php) page. From this point on we only have to care about the session token when navigating inside our add-on.

### MSID

To help integration developers to keep track of user sessions, Emarsys B2CMC provides a unique master session ID (MSID) for each login. This MSID should be bound to the own session of the add-on. The MSID is passed as a query parameter to the iframe of the add-on. ([Here is a code example for this.](/src/SampleIntegration/Middleware/SessionMiddleware.php))

### Session validator

To keep user sessions between integrated applications and the Emarsys B2CMC in sync, you will also have to communicate with the session validator service. The [validator](src/SampleIntegration/SessionValidator.php) keeps track of all logged-in users on Emarsys side, thus can be used to terminate the attached add-on sessions when they log out.
To achieve this central logout feature your application has to ask the validator service every couple of minutes whether the session is still considered to be alive.
Session validator offers a RESTful API to perform these checks. You can find out more about this topic on the partner documentation site.*

To interact with this service we'll provide a separate set of Escher credentials which may only be used for communication between the add-on and the session validator.

### Validation workflow

In our implementation, the entire process of session validation is done by the [session middleware](/src/SampleIntegration/Middleware/SessionMiddleware.php).
It first checks whether the request contains a valid JWT token, then continues with the validation of the MSID using the session validator service.

> Please note that the communication with the session validator has very strict service-level agreements (SLA) towards the validator service. We communicate with it only every couple of minutes and cache its response. Also, in the case of a failure (timeout, network/server error) the session should be considered alive. However, if the validator indicates that the session is terminated, all further requests with that MSID should be rejected.

## Automation Center integration

An add-on may enrich the Emarsys B2CMC's built-in visual workflow editor with custom nodes. You may develop entry nodes which can be used to trigger these workflows and action nodes which can provide useful functionalities (e.g. send push messages) by triggering external services.

![Workflow](/docs/sample_workflow.png?raw=true "Sample workflow")

### Entry node

Automation Center (AC) nodes can be used to trigger one or more automated workflows in the B2CMC for one contact at the time. This basically means, that the trigger accepts contact IDs (one in each trigger call) not entire user lists (which may contain a batch of contacts).

![Entry node](/docs/entry_node.png?raw=true "Transactional entry node")

#### Node options

Please note that there is a significant difference between entry nodes and (external) [event triggers](http://documentation.emarsys.com/resource/developers/endpoints/external-events/trigger-event/).
Firing an event will automatically trigger all attached AC workflows.
Entry nodes provide a more configurable way for doing this by allowing each instance of the node to be bound to a specific "resource". This enables you to trigger only a subset of these node instances.

[In our entry node example](/src/SampleIntegration/Controller/EntryNode) we simulate a package delivery company. A node instance can be configured to be triggered by specific steps of the shipping process (initiated, picked up, in transit, delivered).
So when you trigger the node with the "picked up" resource it will only start the workflows which start with the entry node configured to these specific events. Other programs containing the same type of node, but configured to listen to the "delivered" resource will not be triggered.
As a result of this, you will not have to create a separate type of node for each event, just one for each event group (in our case this is delivery status change).

![Node settings](/docs/entry_node_options.png?raw=true "Action node settings")

You will have to provide this node configuration to use via a URL. This URL may point to an actual web page (which we embed into the node settings dialog) or an options JSON (from which we populate a dropdown select).
The URL will be loaded into an iframe using the Escher (pre-)signed of the provided URL or be fetched via an Escher signed HTTP GET request. For security reasons you will have to validate the signature. (These request will use the own credential scope of the service.)

> In this entry node we demonstrate the JSON formatted options.
The options are listed in the [EntryNode\OptionsController's](/src/SampleIntegration/Controller/EntryNode/OptionsController.php) index action. An option name should always be human readable and is only used on the UI, the trigger events have to rely on the ID of them.

#### Triggering the workflow

The actual triggering mechanism is done by the [EmarsysClient's startAutomationCenterPrograms method](/src/SampleIntegration/EmarsysClient.php).
The trigger event must be done via an Escher-signed HTTP POST request. For this you have to use eu/suite/ems_request as credential scope.

> Please note that although we provided a user interface for triggering the node with several [resource IDs](src/SampleIntegration/Controller/EntryNode/OptionsController.php), this is only for demonstration purposes. We just fetch the first 100 contacts of the given customer and you may trigger each delivery state for them.

### Action node

Your add-on's functionalities may be made available inside AC workflows by developing new action nodes. A good example would by a node that sends SMS or mobile push notifications to contacts.

Async API nodes can be used to receive notifications when one or multiple contacts reach the node in the workflow. Then the add-on can perform the custom functionality on it's side and notify the AC that the node has finished it's task and the contact (or contacts) may continue their journey.
You may also implement a filter functionality on contacts using such a node. You can stop the workflow of a contact (or contacts) by not notifying the AC or by notifying it with only a subset of contacts (who may continue).

![Node settings](/docs/confirmation_workflow.png?raw=true "Action node settings")

[In our example](/src/SampleIntegration/Controller/ConfirmationNode) we implement a manual contact pass-through confirmation.
Contacts who enter the node will be tagged with a user defined text and then be visible on the Confirmation tab of the add-on's integrated UI. On this interface you may manually allow them continue their journey in the workflow or drop them from it.
Please keep in mind that this kind of behavior is not supported and is only used for demonstration purposes.

#### Node options

Similarly to the entry nodes, action nodes may also provide settings. This can be done via a JSON-formatted option list or embedding an iframe.

In the included example, we chose to implement the action node configuration via an embeddable UI.

![Node settings](/docs/confirmation_node_options.png?raw=true "Action node settings")

Our option UI for this node consists of only one editable input. The content of this input will be used to generate a [resource](src/SampleIntegration/Controller/EntryNode/OptionsController.php) to identify this instance of the node.

When the user clicks **Save** on the settings dialog, Emarsys will send a javascript `Window.postMessage()` event to the iframe with the message "resource.save". As a response, the option iframe must send back another post message. This response must specify an ID which will be used as the resource ID (when communicating with the node's back-end) and a label which will displayed below the node on the AC's editor UI.

> Please note, that in case of an action node, Automation Center will reach out to the back-end of the node, so beside the options URL you will have to provide a trigger URL.

#### Contact entry

When a contact enters the node, then AC will make a HTTP POST request to the add-on's trigger URL. The payload will contain the ID of the customer for whom the program was set up, the ID of the user who entered the node, the resource ID (which was set in the node options), the ID of the program, the ID of the exact node instance and the ID of the trigger event.
AC uses different trigger ID for every call of the trigger URL, however, in some error scenarios it may retry the trigger request with the same trigger ID. The add-on should be ready for this (e.g. not sending the same SMS twice to the same contact).
You may see the actual usage in the [ConfirmationNode\TriggerController's](src/SampleIntegration/Controller/ConfirmationNode/TriggerController.php) index action.

> Please note that the trigger may also be called with a list of users. In this case a user list ID will be passed instead of the user ID.

The add-ons trigger URL must send a timeout in the response when it is triggered.
The timeout will be used as a deadline for your add-on, to drop the contact (or list of contacts) from the workflow or put them back.

#### Contact exit

To let contacts continue their journey in the AC workflow, the add-on must make a request to the Emarsys API with the trigger ID and the contact (or contact list) ID which needs to continue the workflow.

> Please note that if you wish to drop the contact from the workflow you have to call the Emarsys API with 0 instead of the contact's ID. If the node was called with a batch of contacts you may drop just certain contacts (by not including them in the user list you push back) or drop all of them by providing 0 as the user list ID.

> When allowing contacts (or a list of contacts) to continue the workflow you may only provide IDs of those whom Automation Center triggered the add-on with.

Although this is a must when developing AC action nodes, in our sample we did not implement batch contact handling.

## Setup and run project

The _docker/_ subdirectory of the project contains all necessary dependencies to run the project. It uses a PHP 7.1 container to run the application and an official PostgreSQL container as persistent data storage.

Before you start the add-on (locally or a deployed version) you will have to set some environment variables:

| variable name | description |
| ------------- | ----------- |
| SUITE_URL | URL of the Emarsys B2CMC API (usually https://api-proxy.s.emarsys.com or https://api.emarsys.net) |
| ESCHER_KEY_ID | name of the Escher key which is used for two-way communication with the Emarsys B2CMC |
| ESCHER_SECRET | the secret which belongs to the _ESCHER_KEY_ID_ and will be used to sign the requests between Emarsys and the add-on |
| JWT_TOKEN_SECRET | will be used to sign the session (JWT) tokens |
| SESSION_VALIDATOR_URL | URL of the Emarsys session validator service |
| SESSION_VALIDATOR_KEY_ID | name of the Escher key which is used for communication with the session validator |
| SESSION_VALIDATOR_SECRET | the secret which belongs to the _SESSION_VALIDATOR_KEY_ID_ and will be used for signing requests between the session validator and the add-on |

The variables may be substituted with any value locally. If you wish to set this integration up to work with an Emarsys Provided sandbox account you will have to use the keys and settings provided by Emarsys.

The following commands should start the application in the local development environment.

```
composer install
cd docker
docker-compose up
```

After this you may interact with the app using Escher signed HTTP requests (for login and event triggers).

## Questions & Support

If you are an Emarsys Partner and have further questions regarding the integration process or this specific project, please contact Emarsys Support.
