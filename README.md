## bb API
REST API for bbPress.
 
### Finished Endpoints
#### Get Forums List
```
wp-json/bb-api/v1/forums
```
| Perameters | Description |
| ------ | ------ |
| _embed | Whether to show sub forums assigned to object. |

#### Get Forum
```
wp-json/bb-api/v1/forums/{id}
```

#### Get Forum Children
```
wp-json/bb-api/v1/forums/{id}/forum
```

#### Get Topics
```
wp-json/bb-api/v1/topics
```
| Perameters | Description |
| ------ | ------ |
| per_page | Maximum number of items to be returned in result set.|
| page | Current page of collection. |


#### Get Forum Topics
```
wp-json/bb-api/v1/forums/{ForumId}/topics
```
| Perameters | Description |
| ------ | ------ |
| per_page | Maximum number of items to be returned in result set.|
| page | Current page of collection. |


#### Get Topic
```
wp-json/bb-api/v1/topics/{id}
```
| Perameters | Description |
| ------ | ------ |
| _embed | Whether to show replies assigned to object.|
| per_page | Maximum number of replies to be returned in result set.|
| page | Current page of replies collection. |

### ToDo
- Add reply endpoint
- edit reply endpoint
- add topic endpoint
- edit topic endpoint
- Dashboard Options 
