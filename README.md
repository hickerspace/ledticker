Control Lunartec LED tickers (PE-9441). **PHP and Python implementations** of [Veit Wahlich's LED Ticker](http://home.ircnet.de/cru/ledticker/).

# Summary
This code converts your text (420 chars max per message) into a valid protocol message the LED ticker understands and displays.

# Use Case
The PHP implementation is designed for e.g. routers connected to this kind of led tickers, which cannot run the original Perl version. A webserver can provide the valid final message and serve it to the router. The router could request the message via a simple wget solution and pipe it to the USB-to-RS232 converter connected to the LED ticker. The logic for generating texts can be easily handled on the server side.

We now use the Python implementation in our flask [REST-API](https://github.com/hickerspace/REST-API) instead. In case you want the messages to be created locally, just use the Python variant.
