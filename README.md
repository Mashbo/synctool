**This is an experimental library. Certain things will not work and support will be limited**

# Usage

Download / build `.phar` file then:

```bash
sync.phar pull \
    --remote.host=remote \
    --remote.port=22 \
    --remote.user=root \
    --remote.db.name=remote \
    --remote.db.user=root \
    --remote.db.name=remote \
    --remote.path=/files \
    --local.path=/tmp/files \
    --local.db.name=local \
    --local.db.user=root
```
