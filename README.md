# Project Title

Triplesss. Super. Simple. Social.

## Getting Started

TODO: PSR7 stuff

### Prerequisites

PHP7.2, MySQL5.x

```
Give examples
```

### Installing

composer add smartsite/triplesss

# New user

$user = new User('Fred');

# New channel

$channel = new Channel();

$channel->setOwner($user);

# New feed

$feed = new Feed();

$feed->setOwner($user);

# Add the feed to a channel

$channel->addFeed($feed);

# Create a post

$post = new Post();

$text = new Text();

$image = new Image();

$post->add($text);

$post->add($Image);

$feed->add($post);

# Hide the post

$post->setVisibity('me');

# Unhide the post

$post->setVisibity('all');

# Like the post

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

* Hat tip to anyone whose code was used
* Inspiration
* etc
