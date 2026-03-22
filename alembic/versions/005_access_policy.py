"""Add access_policy and access_policy_entries tables

Revision ID: 005
Revises: 004
Create Date: 2026-03-22 00:00:00.000000
"""
from typing import Sequence, Union
from alembic import op
import sqlalchemy as sa

revision: str = '005'
down_revision: Union[str, None] = '004'
branch_labels: Union[str, Sequence[str], None] = None
depends_on: Union[str, Sequence[str], None] = None


def upgrade() -> None:
    op.create_table(
        'access_policy',
        sa.Column('id', sa.Integer(), primary_key=True),
        sa.Column('mode', sa.String(20), nullable=False, server_default='open'),
        sa.Column('updated_at', sa.DateTime(timezone=True), server_default=sa.func.now()),
    )
    # Singleton row – always id=1
    op.execute("INSERT INTO access_policy (id, mode) VALUES (1, 'open')")

    op.create_table(
        'access_policy_entries',
        sa.Column('id', sa.Integer(), primary_key=True),
        sa.Column('policy_id', sa.Integer(),
                  sa.ForeignKey('access_policy.id', ondelete='CASCADE'), nullable=False),
        sa.Column('entity_type', sa.String(20), nullable=False),
        sa.Column('entity_id', sa.BigInteger(), nullable=False),
        sa.Column('entity_name', sa.String(255), nullable=True),
        sa.Column('created_at', sa.DateTime(timezone=True), server_default=sa.func.now()),
        sa.UniqueConstraint('policy_id', 'entity_type', 'entity_id', name='uq_policy_entry'),
    )
    op.create_index('ix_ape_policy_id', 'access_policy_entries', ['policy_id'])


def downgrade() -> None:
    op.drop_table('access_policy_entries')
    op.drop_table('access_policy')
