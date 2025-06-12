// Add error handling for jQuery ready
if (typeof jQuery == 'undefined') {
    console.error('jQuery is not loaded');
} else {
    $(document).ready(function () {
        // Initialize variables at the top
        let lastScrollTop = 0;
        let progressTimer = null;
        let isScrolling = false;

        // Scroll handling with debounce
        function handleScroll() {
            const st = $(window).scrollTop();
            const topBar = $(".topBar");
            const topBarHeight = topBar.outerHeight() || 0;

            if (st > lastScrollTop && st > topBarHeight) {
                topBar.removeClass("scrolled-down").addClass("scrolled-up");
            } else {
                topBar.removeClass("scrolled-up").addClass("scrolled-down");
            }
            lastScrollTop = st <= 0 ? 0 : st;
        }

        // Debounce scroll events
        $(window).on('scroll', function () {
            if (!isScrolling) {
                window.requestAnimationFrame(function () {
                    handleScroll();
                    isScrolling = false;
                });
                isScrolling = true;
            }
        });

        // Video controls with null checks
        function volumeToggle(button) {
            const video = $(".previewVideo")[0];
            if (!video) {
                console.warn('Video element not found');
                return;
            }

            video.muted = !video.muted;
            const $icon = $(button).find("i");
            $icon.toggleClass("fa-volume-mute fa-volume-up");
        }

        function previewEnded() {
            const $previewVideo = $(".previewVideo");
            const $previewImage = $(".previewImage");

            if ($previewVideo.length && $previewImage.length) {
                $previewVideo.add($previewImage).toggle();
            }
        }

        // Navigation with better history handling
        function goBack() {
            try {
                if (document.referrer && document.referrer.includes(window.location.hostname)) {
                    window.history.back();
                } else {
                    window.location.href = "index.php";
                }
            } catch (e) {
                window.location.href = "index.php";
            }
        }

        // Video progress tracking with cleanup
        function cleanupVideoListeners(video, callbacks) {
            if (!video) return;
            callbacks.forEach(({event, handler}) => {
                video.removeEventListener(event, handler);
            });
        }

        function initVideo(videoId, username) {
            if (!videoId || !username) {
                console.error("Video ID or username is missing");
                return;
            }

            // Clear any existing timers
            if (progressTimer) {
                clearInterval(progressTimer);
                progressTimer = null;
            }

            startHideTimer();
            setStartTime(videoId, username);
            setupProgressTracking(videoId, username);
        }

        function setupProgressTracking(videoId, username) {
            const video = $("video")[0];
            if (!video) {
                console.warn('Video element not found for progress tracking');
                return;
            }

            const onPlaying = () => {
                clearInterval(progressTimer);
                progressTimer = setInterval(() => {
                    if (!video.paused) {
                        updateProgress(videoId, username, video.currentTime);
                    }
                }, 3000);
            };

            const onEnded = () => {
                clearInterval(progressTimer);
                setFinished(videoId, username);
                cleanupVideoListeners(video, [
                    {event: 'playing', handler: onPlaying},
                    {event: 'ended', handler: onEnded}
                ]);
            };

            video.addEventListener("playing", onPlaying, {once: true});
            video.addEventListener("ended", onEnded, {once: true});
            addDuration(videoId, username);
        }

        // AJAX helpers with better error handling
        function makeAjaxRequest(url, data, successCallback) {
            if (!url) {
                console.error('URL is required for AJAX request');
                return;
            }

            return $.ajax({
                url: url,
                method: 'POST',
                data: data,
                dataType: 'json',
                timeout: 10000, // 10 second timeout
                cache: false
            })
                .done(function (response) {
                    if (successCallback && typeof successCallback === 'function') {
                        successCallback(response);
                    }
                })
                .fail(function (jqXHR, textStatus, errorThrown) {
                    console.error(`AJAX request to ${url} failed:`, {
                        status: textStatus,
                        error: errorThrown,
                        response: jqXHR.responseText
                    });
                });
        }

        // Rest of the functions with proper null checks
        function addDuration(videoId, username) {
            if (!videoId || !username) return;
            makeAjaxRequest("ajax/addDuration.php", {
                videoId: videoId,
                username: username
            });
        }

        function updateProgress(videoId, username, progress) {
            if (!videoId || !username || progress === undefined) return;
            makeAjaxRequest("ajax/updateDuration.php", {
                videoId: videoId,
                username: username,
                progress: progress
            });
        }

        function setFinished(videoId, username) {
            if (!videoId || !username) return;
            makeAjaxRequest("ajax/setFinished.php", {
                videoId: videoId,
                username: username
            });
        }

        function setStartTime(videoId, username) {
            if (!videoId || !username) return;

            makeAjaxRequest("ajax/getProgress.php", {
                videoId: videoId,
                username: username
            }, function (response) {
                if (!response) return;

                const video = $("video")[0];
                const time = parseFloat(response);

                if (video && !isNaN(time) && time > 0) {
                    video.currentTime = time;
                }
            });
        }

        // UI Controls with better error handling
        function restartVideo() {
            const video = $("video")[0];
            if (video) {
                video.currentTime = 0;
                video.play().catch(e => {
                    console.error("Video play failed:", e);
                });
                $(".upNext").fadeOut();
            }
        }

        function watchVideo(videoId) {
            if (!videoId) {
                console.error('No video ID provided');
                return;
            }
            window.location.href = `watch.php?id=${encodeURIComponent(videoId)}`;
        }

        function showUpNext() {
            const $upNext = $(".upNext");
            if ($upNext.length) {
                $upNext.fadeIn();
            }
        }

        // Initialize tooltips if Bootstrap is available
        if (typeof $.fn.tooltip === 'function') {
            $('[data-toggle="tooltip"]').tooltip();
        }

        // Keyboard shortcuts with better event handling
        function handleKeyDown(e) {
            // Ignore if typing in an input field
            if (e.target.tagName === 'INPUT' || e.target.tagName === 'TEXTAREA') {
                return;
            }

            const video = $("video")[0];
            if (!video) return;

            switch (e.key.toLowerCase()) {
                case " ":
                case "k":
                    e.preventDefault();
                    video.paused ? video.play() : video.pause();
                    break;
                case "m":
                    e.preventDefault();
                    video.muted = !video.muted;
                    break;
                case "arrowleft":
                    e.preventDefault();
                    video.currentTime = Math.max(0, video.currentTime - 10);
                    break;
                case "arrowright":
                    e.preventDefault();
                    video.currentTime = Math.min(video.duration, video.currentTime + 10);
                    break;
                case "f":
                    e.preventDefault();
                    if (video.requestFullscreen) {
                        video.requestFullscreen().catch(e => {
                            console.error('Fullscreen error:', e);
                        });
                    }
                    break;
            }
        }

        // Event delegation for dynamically added elements
        $(document)
            .off('click', '.volume-btn')
            .on('click', '.volume-btn', function () {
                volumeToggle(this);
            });

        // Add keyboard event listener
        $(document).off('keydown', handleKeyDown).on('keydown', handleKeyDown);

        // Expose only necessary functions to global scope
        window.volumeToggle = volumeToggle;
        window.previewEnded = previewEnded;
        window.goBack = goBack;
        window.restartVideo = restartVideo;
        window.watchVideo = watchVideo;
        window.showUpNext = showUpNext;
        window.initVideo = initVideo;

        // Cleanup on page unload
        $(window).on('beforeunload', function () {
            if (progressTimer) {
                clearInterval(progressTimer);
            }
            $(document).off('keydown', handleKeyDown);
        });
    });
}