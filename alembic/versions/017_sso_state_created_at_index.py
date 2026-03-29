"""Add index on sso_states.created_at for cleanup query

Revision ID: 017_sso_state_created_at_index
Revises: 016_add_missing_indexes
Create Date: 2026-03-30
"""

from alembic import op

revision = "017_sso_state_created_at_index"
down_revision = "016_add_missing_indexes"
branch_labels = None
depends_on = None


def upgrade():
    op.create_index("ix_sso_states_created_at", "sso_states", ["created_at"])


def downgrade():
    op.drop_index("ix_sso_states_created_at", table_name="sso_states")
