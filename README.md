### Podcast Backend Task
[![Build Status](https://travis-ci.org/ahmedbodi/PodcastBackend.svg?branch=master)](https://travis-ci.org/ahmedbodi/PodcastBackend)

## Design Decisions
In a large scale scenario, You'd want to have multiple different qualities of an audio file so the Episodes entity would have a relationship to a MediaFile entity.
This would hold the following:
 - Metadata regarding the File
 - Download URL
 - Quality
 - Filesize

The metadata is currently processed as soon as a file is uploaded in the same task.
If lots of file's were being uploaded then it'd be a good idea to offload the work to a task queue like RQ or Celery

