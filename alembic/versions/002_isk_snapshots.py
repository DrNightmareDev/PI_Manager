"""Add isk_snapshots table

Revision ID: 002
Revises: 001
Create Date: 2026-03-22 00:00:00.000000
"""
from typing import Sequence, Union
from alembic import op
import sqlalchemy as sa

revision: str = '002'
down_revision: Union[str, None] = '001'
branch_labels: Union[str, Sequence[str], None] = None
depends_on: Union[str, Sequence[str], None] = None


def upgrade() -> None:
    op.create_table(
        'isk_snapshots',
        sa.Column('id', sa.Integer(), nullable=False),
        sa.Column('account_id', sa.Integer(), nullable=False),
        sa.Column('recorded_at', sa.DateTime(timezone=True), server_default=sa.text('now()'), nullable=True),
        sa.Column('isk_day', sa.String(length=50), nullable=False),
        sa.Column('colony_count', sa.Integer(), nullable=False, server_default='0'),
        sa.ForeignKeyConstraint(['account_id'], ['accounts.id'], ondelete='CASCADE'),
        sa.PrimaryKeyConstraint('id'),
    )
    op.create_index('ix_isk_snapshots_id', 'isk_snapshots', ['id'])
    op.create_index('ix_isk_snapshots_account_id', 'isk_snapshots', ['account_id'])


def downgrade() -> None:
    op.drop_index('ix_isk_snapshots_account_id', table_name='isk_snapshots')
    op.drop_index('ix_isk_snapshots_id', table_name='isk_snapshots')
    op.drop_table('isk_snapshots')
