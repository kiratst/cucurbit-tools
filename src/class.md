### interface:
#### QueryInterface

- table()
- one()
- all()
- insert()
- update()
- delete()

### class:
#### Query: 实现QueryInterface, 执行sql
- table()
- one()
- all()
- insert()
- update()
- delete()

#### Builder:处理构造sql
- table()
- select()
- where()
- groupBy()
- orderBy()
- get()
- insert()
- update()
- delete()
- find()
- first()
- sum()
- min()
- max()
- join()
- toSql()
- runSql()

#### Model: Model基类

```
1. connctorFactory
    创建connector
2. ConnectorInterface:
    建立链接
3. QueryInterface:
    查询/执行
4. BuilderInterface:
    构造sql对象
5. SqlParser
    解析sql

```