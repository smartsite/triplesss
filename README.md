# Triplesss

## Super. Simple. Social.

A simple, yet reasonably well-featured social media platform which presents a RESTful API instead of a difficult to customise GUI. It means you can bolt it up to whatever front-end you like -  VueJs, React, even plain old HTML5 + pure Javascript. 

Tags are integral to how Triplesss works. They present an easy way to group, categorise and search for posts that tend to get easily lost on other social platforms.

## Getting Started

TODO: Package stuff

### Prerequisites

PHP7.2, MySQL5.x

```
Give examples
```

### Installing

composer add smartsite/triplesss

### New user

$user = new User('Fred');

### New channel

$channel = new Channel();

$channel->setOwner($user);

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


## Running the tests

TODO:

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

* Inspired by facebook's klunky, tired looking UX and how difficult Facebook and Insta are in regard to finding content you engaged with.
