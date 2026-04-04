# EVE PI Manager

> **⚠️ 已归档 — 本项目已被 [PlanetFlow](https://github.com/DrNightmareDev/PlanetFlow.APP) 取代。**
> EVE PI Manager 已停止主动开发。所有功能已移植并在 PlanetFlow 中进行了扩展，PlanetFlow 是当前维护的版本。所有新安装请使用 PlanetFlow。

[Deutsch](README.de.md) | [English](README.en.md) | [ZH-CN](README.zh-Hans.md)

`EVE PI Manager` 是一个面向 EVE Online 的自托管行星工业仪表盘。项目基于 FastAPI、PostgreSQL、Celery 和 RabbitMQ，整合了 PI 监控、规划和运维工具。

如果这个项目对你有帮助，欢迎向 `DrNightmare` 发送游戏内 ISK 赞助。

## 主要功能

- Dashboard：殖民地状态、ISK/天、实时到期倒计时、分页和 CSV 导出
- Characters：主号/小号管理、Token 状态、Scope 刷新、PI 技能总览
- Corporation：跨角色的军团 PI 数据视图
- Inventory、Hauling、Intel、Killboard、Skyhooks、PI Templates
- PI Chain Planner、Colony Assignment Planner、System Analyzer、System Mix、Compare、Fittings
- 通过 Celery + RabbitMQ 执行后台刷新，也支持 APScheduler 回退模式
- 基于 ETag 的 ESI 缓存、市场缓存、Webhook 提醒和 Manager 权限控制
- 支持德语、英语和简体中文界面

## 主要页面

- `Dashboard`
- `Characters`
- `Corporation`
- `Inventory`
- `Hauling`
- `Intel`
- `Killboard`
- `Skyhooks`
- `PI Templates`
- `Jita Market`
- `PI Chain Planner`
- `Colony Assignment Planner`
- `System Analyzer`
- `System Mix`
- `Compare`
- `Fittings`
- `Manager`

## 必需的 ESI Scopes

```text
esi-planets.manage_planets.v1
esi-planets.read_customs_offices.v1
esi-location.read_location.v1
esi-characters.read_corporation_roles.v1
esi-skills.read_skills.v1
esi-fittings.read_fittings.v1
```

如果需要结构搜索和更完整的军团功能，建议额外启用 `esi-search.search_structures.v1`。

## 快速开始

```bash
cp .env.example .env
docker compose up -d
```

至少需要在 `.env` 中填写：

```env
DB_PASSWORD=change_me
EVE_CLIENT_ID=your_client_id
EVE_CLIENT_SECRET=your_client_secret
EVE_CALLBACK_URL=http://your-domain-or-ip/auth/callback
SECRET_KEY=replace_me_with_a_long_random_secret_key
```

注意：

- 使用纯 HTTP 时设为 `COOKIE_SECURE=false`
- 通过 HTTPS 提供服务时设为 `COOKIE_SECURE=true`
- 只有在明确需要回退模式时才把 `CELERY_BROKER_URL` 留空

## Docker Compose

默认服务：

- `db`
- `rabbitmq`
- `app`
- `celery_worker`
- `celery_beat`
- `celery_ws`

可选 profile：

- `nginx`
- `pgbouncer`
- `monitoring`

常用命令：

```bash
docker compose up -d
docker compose logs -f app
docker compose logs -f celery_worker
docker compose logs -f celery_beat
docker compose logs -f celery_ws
```

更新已有 Compose 安装：

```bash
bash scripts/update_compose.sh
```

## 原生 Linux

全新安装：

```bash
sudo bash scripts/setup_linux.sh
```

升级旧版本：

```bash
sudo bash scripts/upgrade_to_latest.sh
```

日常更新：

```bash
sudo bash scripts/update_linux.sh
```

systemd 服务名：

- `eve-pi-manager`
- `eve-pi-manager-worker`
- `eve-pi-manager-beat`
- `eve-pi-manager-ws`

## 原生 Windows

适合本地或较小规模部署。

安装：

```powershell
powershell -ExecutionPolicy Bypass -File .\scripts\setup_windows.ps1
```

更新：

```powershell
powershell -ExecutionPolicy Bypass -File .\scripts\update_windows.ps1
```

Windows 脚本不会自动配置 RabbitMQ 和 Celery，因此小型部署通常会使用 APScheduler 回退模式。

## 管理工具

- `scripts/add_administrator.py`
- `scripts/remove_administrator.py`
- `/manager` 用于访问策略、账号管理、翻译编辑和缓存/错误恢复

## 健康检查

```text
GET /health
```

示例响应：

```json
{
  "status": "ok",
  "database": "ok",
  "rabbitmq": "ok"
}
```

如果没有配置 Celery，RabbitMQ 会显示为 `not_configured`。

## 技术栈

- FastAPI + Jinja2
- PostgreSQL + SQLAlchemy + Alembic
- Celery + RabbitMQ
- Gunicorn / Uvicorn
- Bootstrap 5

## License

MIT。详见 [LICENSE](LICENSE)。
