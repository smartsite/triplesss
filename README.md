# Triplesss

## Super. Simple. Social.

A simple, yet reasonably well-featured social media platform which presents a RESTful API instead of a difficult to customise GUI. It means you can bolt it up to whatever front-end you like -  VueJs, React, even plain old HTML5 + pure Javascript. 

The API and class structure have been strongly influenced by [stream-php](https://github.com/GetStream/stream-php), without depending on https://getstream.io.

Tags are integral to how Triplesss works. They present an easy way to group, categorise and search for posts that tend to get easily lost on other social platforms.

## Getting Started

TODO: Package stuff

### Prerequisites

PHP7.2^, MySQL5.x^

```
Give examples
```

### Installing

composer install smartsite/triplesss

### New user

$user = new User('Fred');

### New channel

$channel = new Channel();

$channel->setOwner($user);

$channel->setName("My cool channel");

### New feed

$feed = new Feed();

$feed->setOwner($user);

### Add the feed to a channel

$channel->addFeed($feed);

### Create a post

$post = new Post();

$text = new Text("Hello, Tripless!");

$imageSrc = file_get_contents("https://en.wikipedia.org/wiki/Lenna#/media/File:Lenna_(test_image).png");

$image = new Image($imageSrc);

$post->add($text);

$post->add($Image);

$feed->add($post);

### Hide the post

$post->setVisibity('me');

### Unhide the post

$post->setVisibity('all');

### Like the post

$reaction = new Reaction('like');

$post->addReaction($user, $reaction);

### Add some tags

$post->addTags('lenna', 'girls', 'hats');

### Update the feed

$feed->update();

### find stuff you're interested in

$feed->findPosts('llamas', 'hats');


## Running the tests

easy tiger... this thing isn't even in alpha yet!

### Break down into end to end tests

TODO:

Image create

Text create

User ceate

Post create

Feed create

... etc.


### And coding style tests

Explain what these tests test and why

if($thing) {

   doSomething();

}

## Deployment

TODO

## Built With

Elbow grease

## Contributing

## Versioning

## Authors

* **Peter Mariner** - *Initial work* - [smartsite](https://github.com/smartsite)

See also the list of [contributors](https://github.com/your/project/contributors) who participated in this project.

## License

This project is licensed under the MIT License - see the [LICENSE.md](LICENSE.md) file for details

## Acknowledgments

* Inspired by facebook's klunky, tired looking UX and how difficult Facebook and Insta are in regard to finding content you engaged with as opposed to getting bombarded with content that makes to want to gouge your eyes out.

* Motivated by Getstream's desire to try and charge you for something you can easily do yourself 
