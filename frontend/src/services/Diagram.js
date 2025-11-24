import axios from '@/axios';
import { useToast } from 'vue-toast-notification';
import store from "@/store/index.js";
import { TableActions } from "./TableActions.js";

const $toast = useToast();
store.dispatch('initializeAuth');

export const Diagram = {
    async get(id) {
        try {
            const response = await axios.get(`/api/diagrams/${id}`);
            return JSON.parse(response.data.schema);
        } catch (error) {
            if (error.response) {
                $toast.error(error.response.data.message);
            } else {
                $toast.error('Something went wrong!');
            }
        }
    },
    import(sqlScript) {
        const TableStyle = {
            display: 'flex',
            border: '1px solid #10b981',
            background: '#007BFF',
            borderColor: '#007BFF',
            color: 'white',
            borderRadius: '5px',
            width: '350px',
            height: '40px',
            alignItems: 'center',
            justifyContent: 'space-between',
        }

        const elements = ref([{}])
        let nodeProps = {};
        let rowProps = {
            rowName: '',
            keyMod: '',
            sqlType: '',
            nullable: false,
            unsigned: false
        }


        let statements = sqlScript.split("\n");
        statements = statements.map(function (statement) {
            statement = statement.toLowerCase()

            return statement
                .replace(/\n/g, '')
                .replace(/\t/g, '')
                .replace(/`/g, '')
                .replace(/--.+/, '').replace(/\/\*.+/, '')
                .replace('--', '')
                .replace(/^set.+/, '').replace('commit;', '')
                .replace('start transaction;', '')
                .replace(/modify\s+.+/, '')
                .replace(/\s+default\s+[a-z]*/, '')
                .replace(/engine.+/, ';');
        });
        statements = statements.join("");
        statements = statements.split(";");

        const alterTableElements = [];
        const createTableElements = [];
        statements.forEach(statement => {
            statement.trim().toLowerCase().startsWith("alter table")
                ? alterTableElements.push(statement)
                : createTableElements.push(statement);
        });
        statements = alterTableElements.concat(createTableElements)


        let primaryKeysArray = []
        let uniqueKeysArray = []
        let indexesArray = []
        let foreignKeysArray = []
        let primaryKeys = {
            table: '',
            rowName: ''
        }
        let uniqueKeys = {
            table: '',
            rowName: ''
        }
        let indexes = [{
            table: '',
            rowName: ''
        }]
        let foreignKeys = {
            table: '',
            rowName: '',
            targetRowName: '',
            targetTableName: '',
            foreignKeyName: '',
            sourceId: '',
            targetId: ''
        }
        let currentTable = '';
        statements.forEach(function (statement) {


            if (statement.trim().startsWith("alter table")) {
                let alterTableStatements = statement.split(',');
                alterTableStatements.forEach(function (alterTableStatement) {

                    let alterTableMatch = alterTableStatement.match(/alter\s+table\s+(.+\s+)+add/);
                    if (alterTableMatch) {
                        currentTable = alterTableMatch[1].trim();
                    }

                    let primaryKeyMatch = alterTableStatement.match(/add\s+primary\s+key\s+\((.+)\)/);
                    if (primaryKeyMatch) {
                        primaryKeys.table = currentTable.trim();
                        primaryKeys.rowName = primaryKeyMatch[1].trim();
                        primaryKeysArray.push(primaryKeys)
                        primaryKeys = {};
                    }

                    let uniqueMatch = alterTableStatement.match(/add\s+unique\s+key\s+.+\((.+)\)/);
                    if (uniqueMatch) {
                        uniqueKeys.table = currentTable.trim();
                        uniqueKeys.rowName = uniqueMatch[1].trim();
                        uniqueKeysArray.push(uniqueKeys)
                        uniqueKeys = {};
                    }

                    let foreignKeyMatch = alterTableStatement.match(/alter\s+table\s+(.+\s+)+add\s+constraint\s+(.+)foreign\s+key\s*\((.+)\)\s*references\s+(.+)\s*\((.+)\)/);
                    if (foreignKeyMatch) {

                        foreignKeys.table = currentTable.trim();
                        foreignKeys.foreignKeyName = foreignKeyMatch[2].trim();
                        foreignKeys.rowName = foreignKeyMatch[3].trim();
                        foreignKeys.targetTableName = foreignKeyMatch[4].trim();
                        foreignKeys.targetRowName = foreignKeyMatch[5].trim();
                        foreignKeysArray.push(foreignKeys)
                        foreignKeys = {};
                    }

                    let indexMatch = alterTableStatement.match(/add\s+key\s+.+\((.+)\)/);
                    if (indexMatch) {
                        indexes.table = currentTable.trim();
                        indexes.rowName = indexMatch[1];
                        indexesArray.push(indexes)
                        indexes = {};
                    }
                });
            }


            if (statement.trim().startsWith("create table")) {
                const tableName = statement.match(/create\s+table\s+([a-zA-Z0-9_]+)/)[1].trim();
                const tableId = TableActions.addTable(elements, TableStyle, tableName);
                nodeProps = {
                    id: tableId,
                    label: tableName,
                    data: {
                        toolbarPosition: 'top',
                        toolbarVisible: true,
                        position: { x: 0, y: 0 },
                    },

                }

                const inBrackets = statement.match(/\((.+)\)/);

                if (inBrackets && inBrackets[1]) {
                    let rows = inBrackets[1].split(/,[^']/);

                    rows.forEach(function (element) {

                        let rowElements = element.split(' ');

                        rowElements = rowElements.filter(item => item !== ""); //removing empty elements

                        if (rowElements.length === 0) {
                            return;
                        }
                        rowProps.rowName = rowElements[0];
                        rowProps.sqlType = rowElements[1].toUpperCase();
                        rowProps.nullable = !rowElements.includes('not');
                        rowProps.unsigned = rowElements.includes('unsigned');

                        if (rowElements.includes('primary')) {
                            rowProps.keyMod = 'Primary';
                            rowProps.unsigned = true;
                        } else {
                            rowProps.keyMod = 'None';
                        }

                        primaryKeysArray.forEach(function (primaryKey) {
                            if (primaryKey.table === tableName && primaryKey.rowName === rowProps.rowName) {
                                rowProps.keyMod = 'Primary';
                                rowProps.unsigned = true;
                            }
                        })
                        indexesArray.forEach(function (index) {
                            if (index.table === tableName && index.rowName === rowProps.rowName) {
                                rowProps.keyMod = 'Index';
                            }
                        })
                        uniqueKeysArray.forEach(function (unique) {
                            if (unique.table === tableName && unique.rowName === rowProps.rowName) {
                                rowProps.keyMod = 'Unique';
                            }
                        })

                        let rowId = TableActions.addRow(elements, nodeProps, rowProps);
                        foreignKeysArray.forEach(function (foreignKey) {

                            if (foreignKey.rowName === rowProps.rowName) {

                                foreignKey.sourceId = rowId;

                            }
                            if (foreignKey.targetRowName === rowProps.rowName) {
                                foreignKey.targetId = rowId;
                            }
                        })
                        rowProps = {
                            rowName: '',
                            keyMod: '',
                            sqlType: '',
                            nullable: false,
                            unsigned: false
                        }
                    });
                }
            }
        });

        foreignKeysArray.forEach(function (foreignKey) {
            TableActions.addEdge(elements, foreignKey.sourceId, foreignKey.targetId)
        })
        return elements.value;
    },
    // async import(id, script) {
    //     try {
    //         const response = await axios.post(`/api/diagrams/sql/import/${id}`,
    //             {
    //                 script: JSON.stringify(script)
    //             });
    //         return response.data;
    //     } catch (error) {
    //         if (error.response) {
    //             $toast.error(error.response.data.message);
    //         } else {
    //             $toast.error('Something went wrong!');
    //         }
    //     }
    // },
    async export(id) {
        try {
            const response = await axios.get(`/api/diagrams/sql/export/${id}`);
            return JSON.parse(response.data);
        } catch (error) {
            if (error.response) {
                $toast.error(error.response.data.message);
            } else {
                $toast.error('Something went wrong!');
            }
        }
    },
    async save(id, schema) {
        try {
            const response = await axios.put(`/api/diagrams/${id}`, {
                id: id,
                schema: JSON.stringify(schema)
            });
            $toast.success(response.data.message)
        } catch (error) {
            if (error.response) {
                $toast.error(error.response.data.message);
            } else {
                $toast.error('Something went wrong!')
            }
        }
    }
}
