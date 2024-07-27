function share (link) {
    if (navigator.share) {
        const shareDta = {
            url: link
        };

        navigator.share(shareDta)
    }
}