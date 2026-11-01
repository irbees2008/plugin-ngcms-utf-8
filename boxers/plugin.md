---
id: "boxers"
name: "rating of boxers"
version: "0.1"
acts: "ppages, index"
actions: "rpc; boxers_ajax.php"
file: "boxers.php"
type: "plugin"
install: "install.php"
deinstall: "uninstall.php"
config: "config.php"
description: "Рейтинги боксеров"
author: "Alexey N. Zhukov (Wolverine)"
author_uri: "http://digitalplace.ru/"
---

Все шаблоны, начинающиеся с conf.***.tpl это шаблоны админской части. 

* conf.add.boxers.form.tpl - добавление боксера
* conf.add.organization.form.tpl - добавление организаций, типа WBC
* conf.add.weight.form.tpl - добавление весовых категорий
* conf.config.tpl - шаблон пункта "Настройки"
* conf.edit.boxers.form.tpl - редактирование боксера
* conf.edit.fights.tpl - редактирование боя
* conf.edit.organization.form.tpl - редактирование организаций
* conf.edit.weight.form.tpl - редактирование весовых категорий
* conf.fights.tpl - шаблон строки одного боя в списке боев в админке
* conf.list.boxers.row.tpl - шаблон строки одного боксера в списке боксеров в админке
* conf.list.boxers.tpl - список боксеров
* conf.list.organization.row.tpl - шаблон строки одной организации в списке организаций в админке
* conf.list.organization.tpl - список организаций
* conf.list.weights.row.tpl - шаблон строки одной категории в списке весовых категорий в админке
* conf.list.weights.tpl - список весовых категорий
* conf.main.tpl - основной шаблон админки внутри которого выводятся списки боксеров, списки весовых категорий, рейтинги и т.п.
* conf.ratings.tpl - управление рейтингами в админке


Все шаблоны, начинающиеся с plugin.***.tpl это шаблоны страниц на сайте. 

* plugin.alphabet.tpl - /plugin/boxers/
* plugin.letter.tpl - - /plugin/boxers/ шаблон для одной буквы
* plugin.rating.all.all.entries.tpl - /plugin/boxers/ratings/ шаблон для ячейки таблицы рейтингов Все весовые - Все организации
* plugin.rating.all.all.tpl - /plugin/boxers/ratings/ сама таблица рейтингов Все весовые - Все организации
* plugin.rating.all.one.tpl - /plugin/boxers/ratings/ шаблон внутри которого выводятся элементы из plugin.rating.entries.all.one.tpl. Все весовые - Одна организация, Одна организация - Все весовые, Одна весовая - Одна организация
* plugin.rating.entries.all.one.tpl - /plugin/boxers/ratings/ шаблон для одного рейтинга Все весовые - Одна организация, Одна организация - Все весовые, Одна весовая - Одна организация
* plugin.ratings.tpl - /plugin/boxers/ratings/ костяк внутри которого выводятся выпадающие списки с выбором весов, рейтингов, истории и сами 	рейтинги
* profile.tpl - профиль боксера /boxer/ИМЯ
