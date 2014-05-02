// idea from https://github.com/cowboy/jquery-tiny-pubsub
(function($) {
    var kimaiPubSub = $({});
    $.subscribe = function() {
        kimaiPubSub.on.apply(kimaiPubSub, arguments);
    };
    $.unsubscribe = function() {
        kimaiPubSub.off.apply(kimaiPubSub, arguments);
    };
    $.publish = function() {
        kimaiPubSub.trigger.apply(kimaiPubSub, arguments);
    };
}(jQuery));