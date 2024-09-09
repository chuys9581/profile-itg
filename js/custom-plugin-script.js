document.addEventListener('DOMContentLoaded', function() {
    console.log('DOM fully loaded and parsed');
    
    var postlikePopup = document.getElementById('postlike-popup');
    var postlikeClosePopupBtn = document.querySelector('.postlike-close-popup');
    
    var images = document.querySelectorAll('.user-post-image');
    console.log('Found ' + images.length + ' images with class user-post-image');

    // Verificar si el objeto postlikePosts está definido
    if (typeof postlikePosts === 'undefined' || !postlikePosts.posts) {
        console.error('Error: No se encontraron publicaciones en postlikePosts.');
        return;
    }

    images.forEach(function(img) {
        img.addEventListener('click', function() {
            var postlikePostId = img.getAttribute('data-post-id');
            var postlikePost = postlikePosts.posts.find(function(record) { 
                return record.id === postlikePostId; 
            });

            if (postlikePost) {
                var postlikePopupImage = document.getElementById('postlike-popup-image');
                var postlikePopupText = document.getElementById('postlike-popup-text');
                
                postlikePopupImage.src = postlikePost.image;
                postlikePopupText.innerHTML = '<h4>' + postlikePost.title + '</h4><p>' + postlikePost.content + '</p>';
                postlikePopup.style.display = 'block';
            } else {
                console.error('Post not found for post ID: ' + postlikePostId);
            }
        });
    });

    postlikeClosePopupBtn.addEventListener('click', function() {
        postlikePopup.style.display = 'none';
    });

    window.addEventListener('click', function(event) {
        if (event.target === postlikePopup) {
            postlikePopup.style.display = 'none';
        }
    });
});
