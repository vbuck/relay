# Relay Service

Message relay service and request processor workflow editor.

## Request Flow

    HTTP Request        -> Relay Router Service   -> Handlers            -> Response
    /service/relay.json -> Service\Relay::execute -> HandlerPool::handle -> {"code": 200, "message":"…", …}

https://docs.google.com/drawings/d/1zAQuBFFM7s2QsmWt6E0pYzpghOhmPJViNqHn7_eeGFs/edit?usp=sharing
