// All the functions!

const addHandler = (element, handler, callback) => {
    element.addEventListener(handler, function(e) {
        e.preventDefault();
        return callback(e, this);                   
    });
}

const showBox = (show, id) => {     
    console.log(id);
    const container = document.getElementById(id);
    
    if(typeof container != 'undefined') {
        if(show == true) {
            container.classList.remove('hidden');
            container.classList.add('fadeIn');
            window.setTimeout(function() {
                container.classList.remove('fadeIn');
            }, 1000)
        } else {
            container.classList.add('hidden');
            container.classList.remove('fadeIn');
        }
    } else {
        console.log("container" + id + " does not exist");
    }    
}

const getCookie = (name) => {
    var match = document.cookie.match(new RegExp('(^| )' + name + '=([^;]+)'));
    if (match) return match[2];
}

const setCookie = (name, value, exdays) => {
    var exdate = new Date();
    exdate.setDate(exdate.getDate() + exdays);
    var c_value = escape(value) + ((exdays == null) ? "" : "; expires=" + exdate.toUTCString());
    document.cookie = name + "=" + c_value;
}

const getScrollPos = () => {
    let doc = document.body;   
    toppos = (window.pageYOffset || doc.scrollTop);   
}

const setScrollPos = () => {
    let doc = document.body;
    doc.scrollTop = toppos;                               
}

const newImage = (src) => {
    let img = document.createElement('img');
    img.src = src;
    return img;
}  

const newSpan = (classname = '') => {
    let span = document.createElement('span');
    span.className = classname;
    return span;
}

const newDiv = (cls = '', id = '') => {
    const div = document.createElement('div');
    if(id != '') {
        div.id = id;
    }
    if(cls != '') {
        div.className = cls;
    }
    return div;
}

// Not implemented yet, but this pattern will replace the individual AJAX calls
// with tider, more flexible apiCall('endpoint/something', 'GET', function(), [key:value]).done(callback()) 

const apiCall = (url, method, callback, params = null) => {
    let data = '';
    if(params) {
        if(method.toLowerCase() == 'get') {
            let qs = [];
            Object.keys(params).forEach(function (key) {            
                qs.push(key + "=" + params[key]);
            });
            url+= '?' +  qs.join('&');
        } else {
            data = JSON.stringify(params);    
        }   
    };

    fetch(url, {
        method: method, 
        body: data                                          
    }) 
    .then(function(response) {
        response.json().then(function(d){   
            return callback(d);
        })
    });    
}

const lowlightelements = (s, el) => {
    el.forEach((el) => { 
        if(s === true) {
            el.classList.add('lowlight');
        } else {
            el.classList.remove('lowlight');
        }    
    });   
}

const lightboxshow = (s) => {
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

const dologout = () => { 
    fetch('/Triplesss/api/logout.php', {
            method: "GET"                                                   
        }
    ).then((response) => {   
        setCookie('feed_id', '', -1);
        window.location.reload();
    })
}


const hideAllMenus = () => {
    const menus = document.querySelectorAll('.post .options');
    menus.forEach((menu) => {
        menu.classList.remove('swoopInRight');
        menu.classList.add('hide');
    });                                    
}


const menuHandler = () => {
    var posts =  document.querySelectorAll('.postcontent');               

    posts.forEach(function(el) {
        var hammertime = new Hammer(el);
        var wrapper =  el.parentElement.parentElement;                      

        /*
        hammertime.on('tap', function(ev) {
            if(ev.tapCount == 2) {                       
                                                                                   
                wrapper.querySelectorAll('.options')[0].classList.remove('hide');
                menushow(wrapper, true);                                                                            
            }

            if(ev.tapCount == 1) {                                                       
               
                if(!wrapper.querySelectorAll('.options')[0].classList.contains('hide')) {
                    lightboxshow(false);
                    menushow(wrapper, false);                                  
                    // hide any open boxes
                }                                
            }           
        });
        */    
    });
}


const notifyIcon = (i) => {
    const icon = ['', 'A', '&#128204;', '&#128172;', 'C', '&#128151;', '&#129305;', '&#129305;', '&#128089;',' &#129324;','&#128115;','&#128163;','&#128169;', '&#9940;'];
    return icon[i];
}


const notification = (n) => {
     
    const notify = newDiv('notify');
    const avatar = newDiv('avatar');
    const noteicon = newDiv('noteicon');
    const noticeType = notifyIcon(n.type);

    noteicon.innerHTML = noticeType;   
    notify.append(avatar);
    let avatar_src = 'img/profile.png'; 

    if(n.avatar) {
        avatar_src = n.avatar;
    }
    const avatar_image = newImage(avatar_src);
    avatar.append(avatar_image);
    notify.append(avatar);

    const notice = document.createElement('p');
    notice.innerHTML = n.message;
    
    notify.append(notice);
    notify.append(noteicon);
    return notify;
}


const notifications = () => {
    const notifications = document.getElementById('notifications'); 
    const heading = document.createElement('h3');
    heading.innerText = 'Notifications';
    notifications.innerHTML = '';
    notifications.append(heading);
    
    const user_id = getCookie('userID'); 
    let url = 'Triplesss/api/notifications.php?userid=' + user_id;

    fetch(url, {
        method: "GET",                                           
    }) 
    .then(function(response) {
        response.json().then(function(d){      
            d.map(function(n) {
                let notify = notification(n);
                notifications.append(notify);
                notify.addEventListener('click', function() {
                    document.getElementById('menu').classList.remove('show');
                })
            })          
        });
    });
} 



const followUser = (e) => {
    const user_id = getCookie('userID'); 
    const target_id = e.target.getAttribute('data-id');
    const action = e.target.getAttribute('data-action');
    const box = document.getElementById('followbox');

    let url = 'Triplesss/api/connection.php?to=' + user_id + '&from=' + target_id + '&action=' + action;
    fetch(url, { 
        method: "GET"
    })
    .then(function(response) {
        response.json().then(function(d){                         
            console.log("sent!");
            console.log(box);
            doSearch();
            connections();
            box.classList.add('lowlight');
        });
    });
}

const connectClick = (e) => {
    const user_id = getCookie('userID'); 
    const target_id = e.target.getAttribute('data-id');
    const action = e.target.getAttribute('data-action');
    const username = e.target.getAttribute('data-username');
    const box = document.getElementById('followbox');

    const connectButton = document.getElementById("connect-request"); 
    const acceptButton = document.getElementById("connect-accept");
    const removeButton = document.getElementById("connect-remove");

    connectButton.classList.remove('hidden');
    acceptButton.classList.remove('hidden');
    removeButton.classList.remove('hidden');

    box.classList.remove('lowlight');   
    box.setAttribute('data-user_id', target_id);

    let msg = '';
    if(action == 'request') {
        acceptButton.classList.add('hidden');
        removeButton.classList.add('hidden');
        msg = 'Send a connect request to ' + username + '?';
    }

    if(action == 'disconnect') {
        acceptButton.classList.add('hidden');
        connectButton.classList.add('hidden');
        msg = 'Disconnect from ' + username + '?';
    }

    if(action == 'accept') {
        removeButton.classList.add('hidden');
        connectButton.classList.add('hidden');
        msg = 'Accept connection request from ' + username + '?';
    }

    box.querySelectorAll('p')[0].innerText = msg;
    document.documentElement.classList.add('lb');                                                
    lightboxshow(true);  
}



const doConnect = (action) => {
                
    const user_id = getCookie('userID'); 
    const followBox = document.getElementById('followbox');
    const target_id = followBox.getAttribute('data-user_id');
    let url = 'Triplesss/api/connection.php?to=' + user_id + '&from=' + target_id + '&action=' + action;
    console.log(url);
   
    fetch(url, { 
        method: "GET"
    })
    .then(function(response) {
        response.json().then(function(d){    
            followBox.classList.add('lowlight'); 
            lightboxshow(false);
            doSearch();
            connections();
        });
    });                
}



let getUserProfile = () => {
    var user_id = getCookie('userID'); 
    let url = '/Triplesss/api/profile.php?userid=' + user_id;

    if(user_id) {
        fetch(url, {
            method: "GET",                                           
        }) 
        .then(function(response) {
            response.json().then(function(d){                                                   
                
                console.log(d);
                d.map(function(el) {
                    const profileImage = document.querySelectorAll('#lg_profile img')[0];
                    const profileText = document.querySelectorAll('.bio-text')[0];
                    const topAvatar = document.querySelectorAll('#avatar img')[0];
                    if(el) {
                        if(el.content_type == 'image') {
                            const src = el.path + '/' + el.link;
                            profileImage.src = src;
                            topAvatar.src = src;
                        }

                        if(el.content_type == 'text') {
                            profileText.innerText = el.content;
                        }
                    }                  
                })                          
            })
        })
    }    
}



const getProfile = () => {
    var user_id = getCookie('userID'); 
    let url = '/Triplesss/api/profile.php?userid=' + user_id;

    fetch(url, {
           method: "GET",                                           
       }) 
       .then(function(response) {
           response.json().then(function(d){                                                   
                            
                d.map(function(el) {
                    const profileImage = document.querySelectorAll('#avatar img')[0];                   
                    if(el) {
                        if(el.content_type == 'image') {
                            const src = el.path + '/' + el.link;
                            profileImage.src = src;
                        }                                   
                    }                              
               })             
           })
       }
   )
}

const feedProfile = (user_id) => {
               
    let url = '/Triplesss/api/profile.php?userid=' + user_id;

    fetch(url, { 
        method: "GET"
    })
    .then(function(response) {
        response.json().then(function(d){    
            d.map(function(el) {
                const profileImage = document.querySelectorAll('#profile .avatar img')[0];
                const profileName = document.querySelectorAll('#profile h3')[0];
                const profileText = document.querySelectorAll('#profile .bio')[0];
                if(el.content_type == 'image') {
                    const src = el.path + '/' + el.link;
                    profileImage.src = src;
                    profileName.innerText = el.user_name;
                } 

                if(el.content_type == 'text') { 
                    profileText.innerText = el.content;
                }  
            })
        });
    });
};





const fieldError = (t, status) => {
    t.classList.add('field_error'); 
    if(status == true) {
        t.classList.remove('field_error');                     
    } 
    return status;
}

const usernameStatus = (avail) => {
    var availableBox = document.getElementById("available");
   
    if(avail == true) {
        availableBox.innerHTML = "&#10006;";                  
        availableBox.style = "color: transparent; text-shadow:0 0 0 rgb(220,20,20)";
        showBox(false, 'details');
       
    } else {
        availableBox.innerHTML = "&#10004;";                  
        availableBox.style = "color: transparent; text-shadow:0 0 0 rgb(20,200,20)";
        showBox(true, 'details');
    }
}         

const checkUser = (username) => {              
  let data = {};
  data.username = username;
  fetch('Triplesss/api/checkuser', {
            method: "POST",
            body: JSON.stringify(data)                           
      }
  ).then(function(response) {                  
      response.json().then(function(d){
            usernameStatus(d);                                    
      })
  });             
}            

const checkNameFieldLength = (t, length) => {
    return t.value.length > length;	
}          

const checkNames = (t) => {                
    return fieldError(t, t.value.length > 2 && isAlpha(t.value));                
}

const checkPostcode = (t) => {               
    return fieldError(t, t.value.length == 4);                
}

const checkPassword = (t) => {
    const mediumRegex = new RegExp("^(((?=.*[a-z])(?=.*[A-Z]))|((?=.*[a-z])(?=.*[0-9]))|((?=.*[A-Z])(?=.*[0-9])))(?=.{6,})");
    const strongRegex = new RegExp("^(?=.*[a-z])(?=.*[A-Z])(?=.*[0-9])(?=.*[!@#\$%\^&\*])(?=.{8,})");
    return fieldError(t, strongRegex.test(t.value));
}

const checkEmail = (t) => {
    const re = /^(([^<>()\[\]\\.,;:\s@"]+(\.[^<>()\[\]\\.,;:\s@"]+)*)|(".+"))@((\[[0-9]{1,3}\.[0-9]{1,3}\.[0-9]{1,3}\.[0-9]{1,3}\])|(([a-zA-Z\-0-9]+\.)+[a-zA-Z]{2,}))$/;
    return fieldError(t, re.test(t.value));
}

const checkAddress = (t) => {                
    return fieldError(t, checkNameFieldLength(t,3));
}

const checkPhone = (t) => {                
    return fieldError(t,  !isNaN(t.value) && t.value.length === 10 );
}          

const addUser = () => {
  
    let conf = document.getElementById("confirm-email");
    let user_id = document.getElementById("userid");
    let data = {};

    data.user_name = document.getElementById('username').value;
    data.first_name = document.getElementById('firstname').value;
    data.last_name = document.getElementById('lastname').value;
    data.email = document.getElementById('email').value;
    data.password = document.getElementById('password1').value;
    data.from_email = 'webmaster@surfsouthoz.com';
    data.reply_email = 'register@surfsouthoz.com';
    conf.innerText =  data.email;

    fetch('Triplesss/api/register', {
                method: "POST",
                body: JSON.stringify(data)                           
        }
    ).then(function(response) {
        
        response.json().then(function(d){
            console.log(d);
            if(d.hasOwnProperty('userid')) {
                var userid = d.userid * 1;
                if(userid > 0) {
                    user_id.value = userid;
                    showBox(false, 'details');
                    showBox(false, 'namecheck');
                    showBox(true, 'confirm');
                } else {
                    // something went wrong :(    
                }
            } else {
                // something went wrong :(    
            }                                                       
        })                   
        
    });
} 



const isAlpha = (text) => {
    if(text.match(/^[0-9a-zA-Z]+$/)) {
        return true;
    } else {                   
        return false;
    } 
}

const validusername = (text) => {
    if(text.length > 4 && text.length < 33 && isAlpha(text) && text.indexOf('surfsouth') == -1) {
        return true;
    } else {
        return false;
    }
}

const checkFields = () => {
    var textFields = document.querySelectorAll("#details input");
    var allgood = 0;
 
    for(var el of Object.entries(textFields)) {
        let val = el[1].value;
        let id = el[1].id;
        const password1 = document.getElementById('password1').value;
        const password2 = document.getElementById('password2').value;
        let f = el[1];                    

        switch (id) {
          
            case "password1":
                allgood+= 1& checkPassword(f);
            break;

            case "password2":
                allgood+= 1 & checkPassword(f);
                allgood+= 1 & fieldError(f, password1 == password2 && password2 != '');  
            break;
            
            case "email":
                allgood+= 1 & checkEmail(f);
            break;
            
            case "firstname":
                allgood+= 1 & checkNames(f);				
            break;
            
            case "lastname":
                allgood+= 1 & checkNames(f);				
            break;                                                    
            
        }                    
    }
    
    if(allgood == 6) {
        addUser();
    }
}  


const verify = () => {              
    const urlParams = new URLSearchParams( window.location.search);
    let key = urlParams.getAll('key');
    let username = document.getElementById('username');
    if(key != '') {
        fetch('Triplesss/api/verify.php?key=' + key, {
            method: "GET"                                              
            }
        ).then(function(response) {                  
            response.json().then(function(d){
                console.log(d);
                if(d.hasOwnProperty('username')) {
                    username.value = d.username;
                    showBox(true, 'keycheck');
                    showBox(false, 'fail');
                } else if(d.hasOwnProperty('error')) {
                    showBox(false, 'keycheck');
                    showBox(true, 'fail');
                }                                  
            })
        });  
    }               
}   






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


const adminMenu = () => {
    let div = newDiv('optionmenu');
    let options = ['Edit', 'Delete', 'Notify'];
    let icons = ['&#9998;', '&#10006;', '&#9873;'];
    options.map((op, i)=> {
        let span = document.createElement('span');
        span.className = op.toLocaleLowerCase();
        span.innerHTML = icons[i];
        span.setAttribute('data-action', op);
        div.append(span);
    })
    return div;    
}



const feedMenu = () => {
    let div = newDiv('optionmenu');
    let options = ['av','Report','Hide'];
    let icons = ['', '&#10029;','&#128374;'];
    options.map((op, i)=> {
        let span = document.createElement('span');
        span.className = op.toLocaleLowerCase();
        span.innerHTML = icons[i];
        span.setAttribute('data-action', op);
        div.append(span);       
    })
    return div;    
}


let feedPostMarkup = (post, container) => {               
                
    const userid = getCookie('userID');
    for(p in post) {
    
        const thePost = post[p];
        const postId = thePost[0].post_id;
        const avatar = thePost[0].avatar;
        const user_name = thePost[0].user_name;  
        const postOwner =  thePost[0].owner; 

        //console.log(thePost[0]);                       

        const postWrap = newDiv('post');
        const contentWrap = newDiv('content-wrap');
        const avatarBubble = newDiv('avatar');                    
        const avatarImage =  document.createElement('img');
        const profileLink = document.createElement('a');
        //profileLink.href = '/userpage/' + user_name;
        profileLink.href = 'javascript:userPageView(' + postOwner + ')';
       
        profileLink.append(avatarImage)                                      

        if(avatar != false) {
            avatarImage.src = avatar;
        } else {
            avatarImage.src = 'img/profile.png';
        }
       
        
        avatarBubble.append(profileLink);                    

        postWrap.id = postId;                   
        const contentWrapImage = newDiv('postcontent image');
        const contentWrapText = newDiv('postcontent text');      
        postWrap.append(avatarBubble);  
        if(userid == 1) {
            postWrap.appendChild(adminMenu());
        } else {
            postWrap.appendChild(feedMenu());                      
            postWrap.appendChild(reactions(thePost));  
        } 
       
        
        const pw = postWrap.querySelectorAll('.optionmenu span');
        pw.forEach(function(el){
            el.addEventListener('click', function() {
                const action = this.getAttribute('data-action');
                console.log(action);
                if(action == 'Report') {
                    const reportBox = document.getElementById('reportbox');
                    reportBox.classList.remove('lowlight');
                    reportBox.setAttribute('data-post-id', postId);
                    lightboxshow(true);
                }
                if(action == 'Hide') {
                    const hideBox = document.getElementById('hidebox');
                    hideBox.classList.remove('lowlight');
                    hideBox.setAttribute('data-post-id', postId);
                    lightboxshow(true);
                }   
                if(action == 'Delete') {
                    const deleteBox = document.getElementById('deletebox');
                    deleteBox.classList.remove('lowlight');
                    deleteBox.setAttribute('data-post-id', postId);
                    lightboxshow(true);
                } 
                if(action == 'Notify') {
                    const notifyBox = document.getElementById('notifybox');
                    notifyBox.classList.remove('lowlight');
                    notifyBox.setAttribute('data-post-id', postId);
                    lightboxshow(true);
                }                      
            });
        })
               
                                                
        thePost.map(function(pc) {
            if(pc.content_type == "text") {
                contentWrapText.innerText = pc.content;
                contentWrap.appendChild(contentWrapText);
            }

            if(pc.content_type == "image") {
                const img =  document.createElement('img');
                img.src = pc.path + "/" + pc.link;
                contentWrapImage.appendChild(img);
                contentWrap.appendChild(contentWrapImage);
            }       
        })                                            
            
        postWrap.appendChild(contentWrap);                          
        container.appendChild(postWrap); 

        postWrap.addEventListener('click', function() {
            document.getElementById('menu').classList.remove('show');
        })
    
    }              
                        
    getReactions();                     
                  
}


const postMenu2 = () => {
    let div = newDiv('optionmenu');
    let options = ['Edit', 'Tag', 'Delete'];
    let icons = ['&#9998;', '#', '&#10006;'];
    options.map((op, i)=> {
        let span = document.createElement('span');
        span.className = op.toLocaleLowerCase();
        span.innerHTML = icons[i];
        span.setAttribute('data-action', op);
        div.append(span);
    })
    return div;    
}



const postMarkup = (post, container) => {               
                
    post.map(function(p) {
        //console.log(p);
        if(p[0]) {
            const postId = p[0].post_id;
            let postWrap = newDiv('post');
            let contentWrap = newDiv('content-wrap');
            postWrap.id = postId;
            //postWrap.setAttribute('data-post_id', postId);
            
            let contentWrapImage = newDiv('postcontent image');
            let contentWrapText = newDiv('postcontent text');            
                              
            //postWrap.appendChild(postMenu3());             
            postWrap.appendChild(postMenu2()); 
            postWrap.appendChild(reactions(p));         
            
            const pw = postWrap.querySelectorAll('.optionmenu span');

            pw.forEach(function(el){
                el.addEventListener('click', function() {
                    const action = this.getAttribute('data-action');
                    console.log(action);
                    postAction(action, el);
                    //if(action == 'Report') {
                        //const reportBox = document.getElementById('reportbox');
                        //reportBox.classList.remove('lowlight');
                        //reportBox.setAttribute('data-post-id', postId);
                        //lightboxshow(true);
                    //}
                });        
            });


            p.map(function(po) {                      
                if(po.content_type == "text") {
                    contentWrapText.innerText = po.content;
                    contentWrap.appendChild(contentWrapText);
                }
                if(po.content_type == "image") {
                    const img =  document.createElement('img');
                    img.src = po.path + "/" + po.link;
                    contentWrapImage.appendChild(img);
                    contentWrap.appendChild(contentWrapImage);
                }
            })

            postWrap.appendChild(contentWrap);                          
            container.appendChild(postWrap);

            contentWrap.addEventListener('click', function() {
                document.getElementById('menu').classList.remove('show');
            })
        }                   
    }); 
    getReactions();               
       
}


const clearPostBox = () => {                
    document.getElementById('add-comment').value = '';
    document.getElementById('image').value = '';
}

const setSelect = (selectObj, index) => {
    Array.from(selectObj.options).forEach((op, i)=>{
        console.log(i);
    });               
}  

let aggregator = (user_id, offset = 0, length = 10) => {

    let container = document.querySelectorAll('.feed')[0];
    //container.innerHTML = '';
    return fetch('Triplesss/api/aggregator.php?userid=' + user_id + '&offset=' + offset + '&length=' + length, {
                method: "GET"                                                    
    }) 
    .then(function(response) {
        response.json().then(function(d){
            feedPostMarkup(d, container); 
            triggered = false; 
            return function(triggered) {
                return triggered;
            }                     
        });
    });       
}

const lazyLoad = (view) => {
    const userid = getCookie('userID');
    if(view == 'feed') {
        offset = count + offset;
        aggregator(userid, offset, count); 
        
    }
}

const userfeed = (feed_id) => {
    let container = document.querySelectorAll('.feed')[0];
    container.innerHTML = '';
    return fetch('/Triplesss/api/feed.php?feed_id=' + feed_id, {
                method: "GET"                                                    
    }) 
    .then(function(response) {
        response.json().then(function(d){
            //postMarkup(d, container);  
            feedPostMarkup(d, container);  
            //menuHandler();                     
        });
    });       
}


const feed = (feed_id) => {
    let container = document.querySelectorAll('.feed')[0];
    container.innerHTML = '';
    return fetch('/Triplesss/api/feed.php?feed_id=' + feed_id, {
                method: "GET"                                                    
    }) 
    .then(function(response) {
        response.json().then(function(d){
            postMarkup(d, container);  
            //menuHandler();                     
        });
    });       
}

const feedlist = () => {
    const userid = getCookie('userID');
    const saved_feedid = getCookie('feed_id');
    const feedSelect = document.getElementById('feed_id');
    fetch('/Triplesss/api/feeds.php?userid=' + userid, {
        method: "GET"                                                    
    }) 
    .then(function(response) {
        response.json().then(function(d){
            const feed_id = d[0].id;
            if(saved_feedid) {
                //feed(saved_feedid);
            } else {
                //feed(feed_id);
            }
           
            if(d.length > 0) {
                d.map(function(r){
                    const option = document.createElement('option');
                    option.text = r.feed_name;
                    option.value = r.id;
                    feedSelect.add(option);
                    if(saved_feedid == r.id) {
                        option.selected = true; 
                    }
                })
            } else {
                // Every user has at least ONE feed so this should neve rhappen
            }
        })
    })
}

const getReportType = () => {
    const reportOptions = document.querySelectorAll('input[name="report"]');
    //const reportTypes = ['nudity', 'graphic', 'racism', 'threat', 'spam'];

    /*
            8 =>  'report_nudity',
            9 =>  'report_graphic',
            10 => 'report_racism',
            11 => 'report_threat',
            12 => 'report_spam'     
    */
  
    for (const ro of reportOptions) {
        if (ro.checked) {
            return "report_" + ro.value;
            break;
        }     
    }
}


const sendReport = (e) => {
               
    const adminUserId = 1;
    const userid = getCookie('userID');
    const groupId = e.parentElement.className.replace("reportbox vivify ", "");
    const postId = e.parentElement.getAttribute('data-post-id');
    const post = document.getElementById(postId);
    const postButton = post.getElementsByClassName('report')[0];            
    e.parentElement.classList.add('fadeOut');
    const reportType = getReportType();
    const url = '/Triplesss/api/notification.php?from_user_id=' + userid + '&to_user_id=' + adminUserId + '&action=' + reportType + '&post_id=' + postId;
    //console.log(url);
   
    fetch(url, {
        method: "GET"                                                    
    }) 
    .then(function(response) {
        response.json().then(function(d){ 
            console.log(d);
        })
    });
     

    /*
    window.setTimeout(function(){
        e.parentElement.classList.add('lowlight');
        lightboxshow(false);
        var wrapper = document.getElementsByClassName('post ' + groupId)[0];                    
        postButton.classList.add('red');
        e.parentElement.classList.remove('fadeOut');
    }, 300)
    */
}


const doPost = (data) => {
    return fetch('/Triplesss/api/post.php', {
                method: "POST",
                body: JSON.stringify(data)                           
    }) 
    .then(function(response) {
        response.json().then(function(d){
            var postId = d.postId; 
            clearPostBox();  
            //console.log(postId);
        })
    })
}



const getTags = (el) => { 
    const data = {};
    const post_id =  el.parentElement.id;    
    const user_id = getCookie('userID');                                        
    data.post_id = post_id; 
    data.user_id = user_id;

    return fetch('/Triplesss/api/post_tags.php?user_id=' + user_id + '&post_id=' + post_id, {
        method: "GET"                                                 
    }) 
    .then(function(response) {                   
        
        response.json().then(function(d){
           // show tags in the textbox   
           let tags = "";
           if(d.tags) {
                if(d.tags.length > 0) {
                    const tag_array = d.tags.split(',');
                    tags = tag_array.map((tag)=>{
                        return "#" + tag;
                    }).join(' ');
                }                            
            }                       
           document.querySelectorAll('#tagbox input')[0].value = tags;                    
        })                    
    })
}


const editTags = (el) => {
    const data = {};
    const post_id =  el.parentElement.getAttribute('data-post_id');    
    const user_id = getCookie('userID');  
    const tags = document.querySelectorAll('#tagbox input')[0].value;               
    data.post_id = post_id; 
    data.user_id = user_id;
    data.tags = tags;

    return fetch('/Triplesss/api/post_tags.php', {
                method: "POST",
                body: JSON.stringify(data)                           
    }) 
    .then(function(response) {
        response.json().then(function(d){
           
           if(d == true) {
                // tags added
           } else {
               // show error modal
           }

           document.getElementById('tagbox').classList.add('lowlight');
           lightboxshow(false);  
           setScrollPos();
           hideAllMenus();
        })
    })
}

const hidePost = (el) => {
    const post_id = el.parentElement.getAttribute('data-post-id');
    const hideBox = document.getElementById('hidebox');
    let postBox = document.getElementById(post_id);
    postBox.parentNode.removeChild(postBox);
    hideBox.classList.add('lowlight');   
    lightboxshow(false);  
    setScrollPos();     
}

const setPostVisibility = (data) => {

    return fetch('/Triplesss/api/post_visibility.php', {
                method: "POST",
                body: JSON.stringify(data)                           
    }) 
    .then(function(response) {
        response.json().then(function(d){
           
           if(d == true) {
                let postBox = document.getElementById(data.post_id);
                postBox.parentNode.removeChild(postBox);
           } else {
               // show error modal
           }

           document.getElementById('deletebox').classList.add('lowlight');
           lightboxshow(false);  
           setScrollPos();
           hideAllMenus();
        })
    })
}

const addPost = () => {
    let img = document.getElementById('image').files[0];
    const user_id = document.getElementById('userid').value;
    const feed_id = document.getElementById('feed_id').value;
    const comment = document.getElementById('add-comment').value;
    let data = {'comment': comment, 'userid': user_id, 'feedid': feed_id, 'basefolder' : '../../storage' };

    // Check if there's an image
    var reader = new FileReader();

    reader.onload = (function(t) {
        return function(e) {
            data.image = e.target.result;                       
            doPost(data).then(function(){
                feed(feed_id);
            });                  
        }
    })(img); 

    //reader.readAsDataURL(img);
    if(typeof img === 'object') {
        reader.readAsDataURL(img);
    } else { 
        // we don't have an image... just text
       data.image = '';
        doPost(data).then(function(){
            feed(feed_id);
        });    
    }
} 

const deletePost = (el) => {

    const post_id = el.parentElement.getAttribute('data-post-id');
    const user_id = getCookie('userID');
    const text = document.getElementById('edit-comment').value;
    let is_admin = false;
    const data = {};

    if(user_id == 1) { // special powers, man!
        is_admin = true;
    }

    data.post_id = post_id;
    data.text = text;
    data.level = -1;
    data.user_id = user_id;
    data.is_admin = is_admin;
    document.documentElement.classList.add('lb');   
    setPostVisibility(data);                  
}

const savePost = (el) => {
    const post_id = el.parentElement.getAttribute('data-post_id');
    const user_id = getCookie('userID');
    const text = document.getElementById('edit-comment').value;             
    const post = document.getElementById(post_id);              
    const postText = post.querySelectorAll('.content-wrap .text')[0];              
    const data = {};

    data.post_id = post_id;
    data.text = text;
    data.user_id = user_id;
    document.documentElement.classList.add('lb');

    return fetch('/Triplesss/api/post_edit.php', {
                method: "POST",
                body: JSON.stringify(data)                           
    }) 
    .then(function(response) {
        response.json().then(function(d){
            //var postId = d.postId;                          
            if(d.text) {
                postText.innerHTML = d.text;                           
            }
           
            document.getElementById('editbox').classList.add('lowlight');
            lightboxshow(false);  
            setScrollPos();             
            hideAllMenus();
        })
    })
}


const postAction = (action, el) => {                
                                          
    if(action == 'Tag' || action == 'Delete' || action == 'Edit' || action == 'Privacy') {
        const boxname = action.toLowerCase() + "box";
        const box = document.getElementById(boxname);      
        const post_id = el.parentElement.parentElement.id;

        box.classList.remove('lowlight');   
        box.setAttribute('data-post_id', post_id);
        document.documentElement.classList.add('lb');
        lightboxshow(true);  
       
        if(action == 'Edit') {
            if(el.parentElement.querySelectorAll('.content-wrap .postcontent.text')) {
                const text =  el.parentElement.parentElement.querySelectorAll('.content-wrap .postcontent.text')[0].innerText;
                box.querySelectorAll('textarea')[0].value = text; 
            }                      
        }    

        if(action == 'Tag') {
            getTags(el);
        }               
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

const gotoPost = (post_id) => {
    console.log(post_id);
}





let getFeedPosts = (feed_id, offset, count, sort_by, filter_options) => {
               
    var url = 'Triplesss/api/feed.php?feed_id=' + feed_id + '&offset=' + offset + '&count=' + count + '&sort_by=' + sort_by + '&filter_options=' + filter_options;

    fetch(url, {
            method: "GET",                                           
        }) 
        .then(function(response) {
            response.json().then(function(d){                                                   
                //console.log(d);
                const container = document.querySelectorAll('.feed')[0];
                addPosts(container, d);
                //addPostEvents();
            })
        }
    )
} 

let profileedit = () => {
    const profilebox = document.querySelectorAll('.profilebox');
    const textBox = document.getElementById('bio-comment');
    const bioText = document.querySelectorAll('.bio-text')[0].innerText;
    textBox.value = bioText;
    lightboxshow(true);
    lowlightelements(false, profilebox);
}  


let postBio = (user_id) => {
    
    let img = document.getElementById('bio-image').files[0];
  
    //const feed_id = document.getElementById('feedid').value;
    const feed_id = 0; // Feed id 0 is bio!
    let comment = document.getElementById('bio-comment').value;
    const profileImage = document.querySelectorAll('#lg_profile img')[0];

    // Check if there's an image
    var reader = new FileReader();

    reader.onload = (function(t) {
        return function(e) {
            var im = e.target.result;
            const data = {'comment': comment, 'image' : im, 'userid': user_id, 'feedid': feed_id, 'basefolder' : '../../storage', 'maxImageWidth': 400 };
            const profilebox = document.querySelectorAll('.profilebox');

            fetch('Triplesss/api/post.php', {
                method: "POST",
                body: JSON.stringify(data)                           
            }) 
            .then(function(response) {
                response.json().then(function(d){
                    //var j = JSON.parse(d)
                    console.log('Updating profile');
                    var postId = d.postId;  
                    if(postId) {
                        lightboxshow(false);
                        lowlightelements(true, profilebox);
                        //getProfile();
                        getUserProfile(); 
                    }                                 
                })
            })                        
        }
    })(img); 
   
    if(typeof img === 'object') {
        // it's a new image
        reader.readAsDataURL(img);
    } else {
        // re-use the old image!
        fetch(profileImage.src)
        .then(function (response) {
            return response.blob();
        })
        .then(function (blob) {
            reader.readAsDataURL(blob);
        });
    }               
} 

let doSearch = () => {
    const user_id = getCookie('userID'); 
    const searchBox = document.getElementById('user-name');
    const user_name = searchBox.value;  
    
    if(user_name != '') {
      
        showBox(true, 'follow');                 
        let url = 'Triplesss/api/search_user.php?userid=' + user_id + '&username=' + user_name;
        
        fetch(url, { 
            method: "GET"
        })
        .then(function(response) {
            const followBox = document.getElementById('follow');
           
            followBox.innerHTML = '';
            const heading = document.createElement('h3');
            heading.innerText = 'Matches';
            followBox.append(heading);
            response.json().then(function(d) {                      
                if(d.length > 0) {
                    d.map(function(p) {
                        p.buttons = ['follow', 'connect'];
                        const profile = listProfile(p);
                        followBox.append(profile);
                    })   
                } else {
                    const empty = {};
                    empty.user_name = "none found";
                    empty.buttons = [];
                    const profile = listProfile(empty);
                    followBox.append(profile);
                }
                                    
            }); 
        }); 
    }     
}

let listProfile = (p) => {
                         
    const follow = newDiv('follow');
    const avatar = newDiv('avatar');
    const buttons = newDiv('buttons');
    const username = document.createElement('h3');
    const userLink = document.createElement('a');
    const postOwner = p.id;
    
    userLink.href = 'javascript:userPageView(' + postOwner + ')';
    
    userLink.innerText = p.user_name;
    username.append(userLink);

    const declineButton = document.createElement('button');
    const followButton = document.createElement('button');
    followButton.setAttribute('data-id', p.id);
    followButton.setAttribute('data-action', 'follow');
    followButton.setAttribute('data-username', p.user_name);
    
    followButton.innerText = 'Follow';              
    followButton.addEventListener('click', function(e) { followUser(e); })
    
    const connectButton = document.createElement('button');
    connectButton.setAttribute('data-id', p.id);
    connectButton.setAttribute('data-username', p.user_name);
    if(p.relation == 'friend') {
        connectButton.innerText = 'Disconnect';  
        connectButton.setAttribute('data-action', 'disconnect');             
        connectButton.addEventListener('click', function(e) { connectClick(e); })
        buttons.append(connectButton);
    } else if(p.relation == 'follow'){
        connectButton.innerText = 'Connect';   
        connectButton.setAttribute('data-action', 'request'); 
        connectButton.addEventListener('click', function(e) { connectClick(e); })  
        followButton.setAttribute('data-action', 'unfollow');
        followButton.innerText = 'Unfollow'; 
        buttons.append(followButton);
        buttons.append(connectButton);  
    } else if (p.relation == 'friend_request') {
        // user has requested a connection                   
        connectButton.innerText = 'Approve';   
        connectButton.setAttribute('data-action', 'accept'); 
        connectButton.addEventListener('click', function(e) { connectClick(e); })  
        buttons.append(connectButton); 
    } else if (p.relation == 'request_friend') {
        // pending connetion to user                   
        connectButton.innerText = 'Requested';   
        connectButton.setAttribute('disabled', true); 
        buttons.append(connectButton); 
    } else {
        connectButton.innerText = 'Connect';   
        connectButton.setAttribute('data-action', 'request');              
        connectButton.addEventListener('click', function(e) { connectClick(e); })
        buttons.append(followButton);
        buttons.append(connectButton);
    }                
           
    
    let avatar_src = 'img/profile.png'; 

    if(p.avatar) {
        avatar_src = p.avatar;
    }
    const avatar_image = newImage(avatar_src);
    avatar.append(avatar_image);    
    follow.append(avatar);
    follow.append(username);
    follow.append(buttons);
    return follow;               
}


let connections = () => {
    const user_id = getCookie('userID'); 
    let url = 'Triplesss/api/connections.php?userid=' + user_id;
    
    fetch(url, { 
        method: "GET"
    })
    .then(function(response) {
        const connectionBox = document.getElementById('connections');
        connectionBox.innerHTML = '';
        const heading = document.createElement('h3');
        heading.innerText = 'Connections';
        connectionBox.append(heading);
        response.json().then(function(d) {                      
            d.map(function(p) {
                p.buttons = ['follow', 'connect'];
                const profile = listProfile(p);
                connectionBox.append(profile);
            })                        
        }); 
    });  
}


const isloggedin = () => { 
               
    const uname = document.getElementById('uname');
    const userid = document.getElementById('userid');
    const wrap = document.querySelectorAll('.wrap')[0];
    const keycheck = document.getElementById('keycheck');
    const menuButton = document.getElementById('menubutton');
   
   
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
               
                //showBox(false, 'user-profile');
                showBox(false, 'follow');
                showBox(false, 'notifications');
                showBox(false, 'connections');
                wrap.classList.remove('loggedout');
                menuButton.classList.remove('hidden');
                getProfile();
                feedlist();                            

            }  else {
                showBox(false, 'profile');
                showBox(false, 'user-profile');
                showBox(false, 'feed');
                showBox(true, 'keycheck');
                showBox(false, 'fail');
                showBox(false, 'addpost');  
                showBox(false, 'follow');
                //logoutButton.classList.add('hidden');
                menuButton.classList.add('hidden');
                wrap.classList.add('loggedout');
               
                document.getElementById('login-heading').innerText = "User login";
                document.querySelectorAll('#login-prompt p')[0].innerText = "";

            }
            //if(callback) { return callback(d); }
        });                   
    })
};


const hideBoxes = () => {
    showBox(false, 'user-profile');  
    showBox(false, 'nouser');   
    showBox(false, 'addpost');       
    showBox(false, 'search');
    showBox(false, 'keycheck');
    showBox(false, 'fail');
    showBox(false, 'success');
    showBox(false, 'feed');  
    showBox(false, 'userpage');  
    showBox(false, 'profile');   
    showBox(false, 'follow');
    showBox(false, 'notifications');  
    showBox(false, 'messages');     
    showBox(false, 'connections');  
}


const dologin = () => {
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
                
                location.reload();
                password.className = '';
                hideBoxes();
                showBox(true, 'feed');
                document.getElementById('uname').innerText = d.username;
                getProfile(); 
                //window.location.reload();
            }                                 
        })
    });   
};


const userPageView = (userid) => {
    hideBoxes();
    showBox(true, 'feed');  
    showBox(true, 'profile');     

    if(userid > 0) {
        feedProfile(userid);
        fetch('/Triplesss/api/feeds.php?userid=' + userid, {
            method: "GET"                                                    
        }) 
        .then(function(response) {
            response.json().then(function(d){
                const feed_id = d[0].id;
                userfeed(feed_id);
            });
        })    
    } else {
        showBox(true, 'nouser');         
    }     
}


const notificationView = () => {
    hideBoxes();
    showBox(true, 'notifications');  
    notifications();
}

const feedView = () => {
    hideBoxes();
    showBox(true, 'feed');   
    aggregator(user_id, offset, count); 
}

const userView = () => {
    hideBoxes();
    showBox(true, 'addpost');   
    showBox(true, 'feed');     
    const feed_select = document.getElementById('feed_id');
    let feed_id = feed_select.value;
    feed(feed_id);     
}

const profileView = () => {
    hideBoxes();
    getUserProfile();             
    showBox(true, 'user-profile');    
}

const welcomeView = () => {
    hideBoxes();
    showBox(true, 'intro');  
}

const followView = () => {
    hideBoxes();
    showBox(true, 'search');  
    showBox(true, 'connections');  
    connections();
}

const messageView = () => {
    hideBoxes();
    showBox(true, 'messages');  
}


const setView = (view) => {
    
    // Menu button calls
    console.log(view);
    currentView = view;
    let feedContainer = document.querySelectorAll('.feed')[0];
    feedContainer.innerHTML = '';
    count = 10;
    offset = 0;
    triggered = false;

   
    switch(view) {
        case "user": 
            userView();
            break;
        
        case "profile": 
            profileView();
            break; 
            
        case "feed": 
            feedView();
            break;  
            
        case "follow": 
            followView();
            break; 
        
        case "notification": 
            notificationView();
            break;               
        
        case "userpage": 
            userPageView(userid);
            break;  

        case "message": 
            messageView();
            break;     
        
        case "login": 
            loginView();
            break;  

        case "logout": 
            dologout();
            break;    
        
        default: 
           //welcomeView();    
           feedView();
    }
}