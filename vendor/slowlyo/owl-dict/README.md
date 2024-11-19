## OwlAdmin 数据字典

## 效果

增加数据字典管理功能

## 安装

#### zip 下载地址

[https://gitee.com/slowlyo/owl-dict/repository/archive/master.zip](https://gitee.com/slowlyo/owl-dict/repository/archive/master.zip)

#### composer

```bash
composer require slowlyo/owl-dict
```

## 使用说明

1. 安装扩展
2. 在扩展管理中启用扩展

## 使用方法

### 配置

在扩展管理中可以配置以下内容

- 屏蔽数据字典类型管理
- 屏蔽数据字典创建
- 屏蔽数据字典删除

### 调用

```php
// 使用助手函数
admin_dict()->getValue('user.status', '未知');
// 使用容器
app('admin.dict')->getValue('user.status', '未知');
// 使用接口获取字典选项 (该接口会返回 [['label' => xx, 'value' => xx]] 格式的数据
/admin_dict/options?path=gender
```

### 可用方法

```php
/**
 * 获取数据字典值
 * @params string $path 路径 例如: user.status
 * @params string $default 默认值
 * @params bool $needAllData 是否需要返回所有数据 (为true时返回所有数据, 包括禁用数据和软删除数据)
 * @return string 例如: 正常
 */
public function getValue($path, $default = '', $needAllData = true)


/**
 * 获取数据字典键
 * @params ...
 * @return string 例如: normal
 */
public function getKey($path, $default = '', $needAllData = true)


/**
 * 获取数据字典数据
 * @params ...
 * @return array 例如: ['key' => 'normal', 'value' => '正常']
 */
public function getAll($path, $default = [], $needAllData = true)


/**
 * 获取数据字典数据 - 选项格式
 * @params ...
 * @return array 例如: [['label' => '正常', 'value' => 'normal']]
 */
public function getOptions($path, $needAllData = true)

```

### 注意事项

数据字典数据做了缓存, 新增或修改时会自动清理缓存  
直接修改数据库数据字典表数据, 请手动清除缓存
