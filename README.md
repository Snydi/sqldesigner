# SQL Designer

[![License](https://img.shields.io/badge/license-AGPL--3.0-blue)](./LICENSE)
[![Website](https://img.shields.io/website?url=https%3A%2F%2Fsql-designer.com)](https://sql-designer.com)

**SQL Designer** is a web-based visual database schema designer with a Free plan and optional Pro subscription. Design and manage database schemas through an intuitive drag-and-drop interface — no SQL expertise required.

🌐 **Live app:** [sql-designer.com](https://sql-designer.com)

![SQL Designer — visual database schema editor](backend/public/images/designer_screenshot.png)

---

## Why SQL Designer?

Most database design tools are either expensive, desktop-only, or require an account just to get started. SQL Designer runs in your browser and gets you from idea to schema in seconds. The Free plan includes 1 diagram and 3 combined exports per day; Pro provides unlimited diagrams and exports.

- **No install** — runs entirely in the browser
- **Visual-first** — drag, drop, and connect tables without writing SQL
- **Bidirectional SQL** — import existing SQL to visualize it, or export clean `CREATE` statements from your diagram
- **Multiple SQL dialects** — support for MySQL, PostgreSQL, SQLite, Oracle, SQL Server, and MS Access
- **Open source** — read the code, report bugs, suggest features

---

## Features

- **Visual diagram editor** — design schemas on an interactive canvas with drag-and-drop support
- **Table & column management** — create, rename, and delete tables and columns inline
- **Relationship visualization** — connect tables with relationship lines using crow's foot notation
- **Support for MySQL, PostgreSQL, SQLite, Oracle, SQL Server, and MS Access** — choose your target database type per diagram
- **SQL import & export** — generate SQL `CREATE` statements from your diagram, or import existing SQL to auto-build a diagram
- **Save & manage diagrams** — store multiple diagrams per account with auto-save
- **User accounts** — register and log in to keep your diagrams private and persistent

---

## Stack

| Layer    | Technology             |
|----------|------------------------|
| Frontend | Vue 3, Vuex, Vue Flow  |
| Backend  | Laravel 12 (PHP 8.2+)  |
| Database | PostgreSQL             |
| Infra    | Docker, Nginx          |

---

## Contributing

Contributions are welcome. Please open an issue first to discuss what you'd like to change.

1. Fork the repository
2. Create a feature branch (`git checkout -b feature/my-feature`)
3. Commit your changes
4. Open a pull request

---

## License

This project is open source under the [GNU Affero General Public License v3.0](./LICENSE).

**Author:** Snyatkov Dmitriy Andreevich
**Contact:** dmitriy@sql-designer.com
