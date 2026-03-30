"""Add corp bridge connections

Revision ID: 020_corp_bridge_connections
Revises: 019_kill_activity_cache
Create Date: 2026-03-30
"""

from alembic import op
import sqlalchemy as sa

revision = "020_corp_bridge_connections"
down_revision = "019_kill_activity_cache"
branch_labels = None
depends_on = None


def upgrade():
    op.create_table(
        "corp_bridge_connections",
        sa.Column("id", sa.Integer(), nullable=False),
        sa.Column("corporation_id", sa.BigInteger(), nullable=False),
        sa.Column("corporation_name", sa.String(length=255), nullable=False),
        sa.Column("from_system_id", sa.BigInteger(), nullable=False),
        sa.Column("from_system_name", sa.String(length=255), nullable=False),
        sa.Column("to_system_id", sa.BigInteger(), nullable=False),
        sa.Column("to_system_name", sa.String(length=255), nullable=False),
        sa.Column("notes", sa.String(length=255), nullable=True),
        sa.Column("created_by_account_id", sa.Integer(), nullable=True),
        sa.Column("created_at", sa.DateTime(timezone=True), server_default=sa.func.now(), nullable=True),
        sa.Column("updated_at", sa.DateTime(timezone=True), server_default=sa.func.now(), nullable=True),
        sa.ForeignKeyConstraint(["created_by_account_id"], ["accounts.id"], ondelete="SET NULL"),
        sa.PrimaryKeyConstraint("id"),
        sa.UniqueConstraint("corporation_id", "from_system_id", "to_system_id", name="uq_corp_bridge_connections_pair"),
    )
    op.create_index(op.f("ix_corp_bridge_connections_id"), "corp_bridge_connections", ["id"], unique=False)
    op.create_index(op.f("ix_corp_bridge_connections_corporation_id"), "corp_bridge_connections", ["corporation_id"], unique=False)
    op.create_index(op.f("ix_corp_bridge_connections_created_by_account_id"), "corp_bridge_connections", ["created_by_account_id"], unique=False)
    op.create_index(op.f("ix_corp_bridge_connections_from_system_id"), "corp_bridge_connections", ["from_system_id"], unique=False)
    op.create_index(op.f("ix_corp_bridge_connections_to_system_id"), "corp_bridge_connections", ["to_system_id"], unique=False)
    op.create_index("ix_corp_bridge_connections_corp_pair", "corp_bridge_connections", ["corporation_id", "from_system_id", "to_system_id"], unique=False)


def downgrade():
    op.drop_index("ix_corp_bridge_connections_corp_pair", table_name="corp_bridge_connections")
    op.drop_index(op.f("ix_corp_bridge_connections_to_system_id"), table_name="corp_bridge_connections")
    op.drop_index(op.f("ix_corp_bridge_connections_from_system_id"), table_name="corp_bridge_connections")
    op.drop_index(op.f("ix_corp_bridge_connections_created_by_account_id"), table_name="corp_bridge_connections")
    op.drop_index(op.f("ix_corp_bridge_connections_corporation_id"), table_name="corp_bridge_connections")
    op.drop_index(op.f("ix_corp_bridge_connections_id"), table_name="corp_bridge_connections")
    op.drop_table("corp_bridge_connections")
