let getCookie = (name) => {
    var match = document.cookie.match(new RegExp('(^| )' + name + '=([^;]+)'));
    if (match) return match[2];
}

let setCookie = (name, value, exdays) => {
    var exdate = new Date();
    exdate.setDate(exdate.getDate() + exdays);
    var c_value = escape(value) + ((exdays == null) ? "" : "; expires=" + exdate.toUTCString());
    document.cookie = name + "=" + c_value;
}

let isloggedin = () => { 
               
    const uname = document.getElementById('uname');
    const userid = document.getElementById('userid');
    const logoutButton = document.getElementById('log-out');
    const wrap = document.querySelectorAll('.wrap')[0];
   
    return fetch('/Triplesss/api/logged_in.php', {
                method: "GET"                                                   
        }
    ).then((response) => {                          
        
        response.json().then((d) => {
                   
            if(d === true) {               
                showBox(false, 'keycheck');
                showBox(false, 'fail');               
                uname.innerText = getCookie('userName'); 
                userid.value = getCookie('userID'); 
                logoutButton.classList.remove('hidden');
                wrap.classList.remove('loggedout');
                getProfile();
                feedlist();                            

            }  else {
                showBox(false, 'profile');
                showBox(false, 'feed');
                showBox(true, 'keycheck');
                showBox(false, 'fail');
                showBox(false, 'addpost');  
                showBox(false, 'follow');
                logoutButton.classList.add('hidden');
                wrap.classList.add('loggedout')
            }
            //if(callback) { return callback(d); }
        });                   
    })
};

let showBox = (show, id) => {
     
    console.log(id);
    if(typeof document.getElementById(id) != 'undefined') {
        if(show == true) {
            document.getElementById(id).classList.remove('hidden');
            document.getElementById(id).classList.add('fadeIn');
            window.setTimeout(function() {
                document.getElementById(id).classList.remove('fadeIn');
            }, 1000)
        } else {
            document.getElementById(id).classList.add('hidden');
            document.getElementById(id).classList.remove('fadeIn');
        }
    } else {
        console.log("container" + id + " does not exist");
    }
    
}

let getScrollPos = () => {
    let doc = document.body;   
    toppos = (window.pageYOffset || doc.scrollTop);   
}

let setScrollPos = () => {
    let doc = document.body;
    doc.scrollTop = toppos;                               
}

let newImage = (src) => {
    let img = document.createElement('img');
    img.src = src;
    return img;
}  

let newSpan = (classname = '') => {
    let span = document.createElement('span');
    span.className = classname;
    return span;
}

let newDiv = (cls = '', id = '') => {
    const div = document.createElement('div');
    if(id != '') {
        div.id = id;
    }
    if(cls != '') {
        div.className = cls;
    }
    return div;
}

let lowlightelements = (s, el) => {
    el.forEach((el) => { 
        if(s === true) {
            el.classList.add('lowlight');
        } else {
            el.classList.remove('lowlight');
        }    
    });   
}


let lightboxshow = (s) => {
    if(s === true) {
        lightbox.classList.add('active');  
        getScrollPos();                                       
    } else {
        
        lightbox.classList.remove('active');  
                          
        window.setTimeout(function() {                        
            document.documentElement.classList.remove('lb');                      
        }, 100)                           
    }
    lowlightelements(s, document.querySelectorAll('.post'));               
}  

let uniqid = (a = "", b = false) => {
    const c = Date.now()/1000;
    let d = c.toString(16).split(".").join("");
    while(d.length < 14) d += "0";
    let e = "";
    if(b){
        e = ".";
        e += Math.round(Math.random()*100000000);
    }
    return a + d + e;
}

let dologout = () => { 
    fetch('/Triplesss/api/logout.php', {
            method: "GET"                                                   
        }
    ).then((response) => {   
        setCookie('feed_id', '', -1);
        window.location.reload();
    })
}

let dologin = () => {
    let data = {};

    const username = document.getElementById('username');
    const password = document.getElementById('password');
    data.username = username.value;
    data.password = password.value;

    fetch('/Triplesss/api/login.php', {
                method: "POST",
                body: JSON.stringify(data)                           
        }
    ).then((response) => {                  
        response.json().then(function(d){
           
            console.log(d);  
            if(d.success == 'false') {
                password.className = 'field_error';
            } else if(d.success == 'true') {
                password.className = '';
                showBox(false, 'keycheck');
                showBox(false, 'fail');
                showBox(true, 'feed');
                document.getElementById('uname').innerText = d.username;
                getProfile(); 
                window.location.reload();
            }                                 
        })
    });   
};

let getComments = (post_id) => {
                       
    return fetch('/Triplesss/api/comments.php?post_id=' + post_id, {
        method: "GET"                                                  
    }) 
    .then(function(response) {
        response.json().then(function(d){
            const comments = d.comments;
            const comments_box = document.getElementById('comments');
            comments_box.innerHTML = '';
           
            comments.map(function(comment) {
                
                const wrap = document.createElement('div');
                wrap.className = 'comment_wrap';
                const username = comment.user_name;
                const text = comment.content; 
                const uspan = document.createElement('span');
                uspan.className = "comment_user";
                const ulink = document.createElement('a');
                ulink.innerText = username;
                ulink.href = '/userpage/' + username;
                uspan.append(ulink);
               
                const cspan = document.createElement('span');
                cspan.className = "comment_text";
                cspan.innerText = text;
                wrap.append(uspan);
                wrap.append(cspan);
                comments_box.append(wrap);                                           
            });
        });
    });
}

let postComment = () => {
    const comment = document.getElementById('post-comment-text').value;
    const user_id = getCookie('userID');
    const post_id = document.getElementById('commentbox').getAttribute('data-post_id');
    const feed_id = document.getElementById('feed_id');
    const post = document.getElementById(post_id);      
    const post_comments = post.querySelectorAll('.reactions .post-comments')[0];               
    let post_count = post_comments.innerText;
    

    const data = {};
    data.comment = comment;
    data.userid = user_id;
    data.postid = post_id;
    data.feedid = feed_id;                
    
    return fetch('/Triplesss/api/comment.php', {
        method: "POST",
        body: JSON.stringify(data)                           
    }) 
    .then(function(response) {
        response.json().then(function(d){
            document.getElementById('post-comment-text').value = '';
            getComments(post_id);
            post_count++;
            post_comments.innerText = post_count;         
        });
    });
}

let reactions = (p) => { 
    if(p) {

        let comment_count = p[0]['comment_count']; 
        let likes = p[0]['likes']; 
        if(p[1]) {
            comment_count = p[1]['comment_count'];
            likes = p[1]['likes'];
        }                    
       
        let div = newDiv('reactions');
       
        
        let comment_button = document.createElement('button');
        comment_button.className = "post-button comment";
        let icon = document.createElement('i');
        icon.className = "material-icons";
        icon.innerText="comment";
        comment_button.append(icon);
        div.append(comment_button);
        let comment_span = document.createElement('span');
        comment_span.className = "post-comments";
        if(comment_count > 0) {
            comment_span.innerText = comment_count;
        }
        div.append(comment_span);
        addReact(comment_button);              
        

        let like_button = document.createElement('button');
        like_button.className = "post-button like";
        icon = document.createElement('i');
        icon.className = "material-icons";
        icon.innerText="favorite";
        like_button.append(icon);
        div.append(like_button);
        let like_span = document.createElement('span');
        like_span.className = "post-likes";
        if(likes > 0) {
            like_span.innerText = likes;
        }
        div.append(like_span);
        addReact(like_button);               
        return div;
    }    
}

let getReactions = () => {
    
    if(typeof getCookie('userID') != 'undefined') {
        const userid = getCookie('userID');
    
        let posts = document.querySelectorAll('.post');
        return fetch('/Triplesss/api/reactions.php?user_id=' + userid, {
            method: "GET"                                     
        }) 
        .then(function(response) {
            let posts = document.querySelectorAll('.post');
            response.json().then(function(d){ 
                
                const reactions = d.reactions;
                Array.from(posts).forEach((post, i)=> {                
                    reactions.map(function(reaction) {
                        if(post.id == reaction.post_id) {
                            const pb = post.getElementsByClassName('like')[0];
                            if(pb) {
                                pb.classList.remove('like');
                                pb.classList.add('liked');
                            }    
                        }
                    })                
                });
            });
        });
    } else {
        return false;
    }    
}  


let addReact = (el) => {
    el.addEventListener('click', function(e) {
        var icon = el.querySelector('i');
        if(el.classList.contains('like')) {
            icon.classList.add('like-button-click');
            window.setTimeout(function(){
                icon.classList.remove('like-button-click');
                icon.parentElement.classList.remove('like');
                icon.parentElement.classList.add('liked');
                const likecount = el.parentElement.querySelector('.post-likes');
                                                          
                const data = {};
                const post_id = el.parentElement.parentElement.id;
                const user_id = getCookie('userID');
                const level = 2; // just a like!

                data.postid  = post_id;
                data.userid  = user_id;
                data.level  = level;
               
                return fetch('/Triplesss/api/reaction.php', {
                    method: "POST",
                    body: JSON.stringify(data)                           
                }) 
                .then(function(response) {
                    response.json().then(function(d){
                        const reactions = d.reactions;
                                                          
                        if(reactions.length > 0) {
                            likecount.innerText = reactions.length;                                       
                        }                                 
                    });
                });                                               
               
            }, 1000)
        }

        if(el.classList.contains('comment')) { 
            lightbox.classList.add('active'); 
            getScrollPos();
            const post_id = el.parentElement.parentElement.id;
            //const user_id = getCookie('userID');
            document.documentElement.classList.add('lb'); 
            document.getElementById('commentbox').classList.remove('lowlight');
            document.getElementById('commentbox').setAttribute('data-post_id',  post_id);
            document.getElementById('post-comment-text').value = '';
            getComments(post_id);
        }                                              
        
    });
}