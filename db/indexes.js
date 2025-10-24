const targetDbName = 'mileapp'
const database = db.getSiblingDB(targetDbName)

function ensureCollection(name) {
  try {
    const exists = database.getCollectionInfos({ name }).length > 0
    if (!exists) {
      database.createCollection(name)
      print(`Created collection: ${name}`)
    }
  } catch (e) {
    print(`Warning ensuring collection ${name}: ${e}`)
  }
}

ensureCollection('tasks')
ensureCollection('users')

try {
  print('Creating tasks indexes...')
  database.getCollection('tasks').createIndexes([
    { key: { status: 1 }, name: 'idx_tasks_status' },
    { key: { created_at: -1 }, name: 'idx_tasks_created_at_desc' },
    { key: { due_date: 1 }, name: 'idx_tasks_due_date' },
    { key: { title: 'text' }, name: 'idx_tasks_title_text' },
  ])
  print('Indexes created for tasks collection.')
} catch (e) {
  print(`Error creating tasks indexes: ${e}`)
}

try {
  print('Creating users indexes...')
  database.getCollection('users').createIndexes([
    { key: { email: 1 }, name: 'idx_users_email_unique', unique: true },
    { key: { created_at: -1 }, name: 'idx_users_created_at_desc' },
    { key: { email_verified_at: -1 }, name: 'idx_users_email_verified_at_desc' },
    { key: { name: 'text', email: 'text' }, name: 'idx_users_name_email_text' },
  ])
  print('Indexes created for users collection.')
} catch (e) {
  print(`Error creating users indexes: ${e}`)
}
