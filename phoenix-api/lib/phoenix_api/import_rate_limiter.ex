defmodule PhoenixApi.ImportRateLimiter do
  use GenServer

  def start_link(_opts) do
    GenServer.start_link(__MODULE__, :ok, name: __MODULE__)
  end

  def reserve(user_id) when is_integer(user_id) do
    GenServer.call(__MODULE__, {:reserve, user_id})
  end

  def reset do
    GenServer.call(__MODULE__, :reset)
  end

  @impl true
  def init(:ok) do
    {:ok, %{per_user: %{}, global: []}}
  end

  @impl true
  def handle_call(:reset, _from, _state) do
    {:reply, :ok, %{per_user: %{}, global: []}}
  end

  @impl true
  def handle_call({:reserve, user_id}, _from, state) do
    cfg = Application.fetch_env!(:phoenix_api, :import_rate_limit)
    now = System.system_time(:second)

    user_window = cfg[:user_window_seconds]
    user_max = cfg[:user_max_requests]
    global_window = cfg[:global_window_seconds]
    global_max = cfg[:global_max_requests]

    global = prune_old(state.global, now, global_window)

    cond do
      length(global) >= global_max ->
        {:reply, {:error, :global_rate_limited}, %{state | global: global}}

      true ->
        user_hits = Map.get(state.per_user, user_id, [])
        user_hits = prune_old(user_hits, now, user_window)

        if length(user_hits) >= user_max do
          {:reply, {:error, :user_rate_limited}, put_user(state, user_id, user_hits, global)}
        else
          new_user_hits = [now | user_hits]
          new_global = [now | global]
          new_per_user = upsert_user(state.per_user, user_id, new_user_hits)

          {:reply, :ok, %{per_user: new_per_user, global: new_global}}
        end
    end
  end

  defp prune_old(timestamps, now, window_sec) do
    cut = now - window_sec
    Enum.filter(timestamps, fn ts -> ts > cut end)
  end

  defp put_user(state, user_id, user_hits, global) do
    per_user = upsert_user(state.per_user, user_id, user_hits)
    %{state | per_user: per_user, global: global}
  end

  defp upsert_user(per_user, user_id, []) do
    Map.delete(per_user, user_id)
  end

  defp upsert_user(per_user, user_id, hits) do
    Map.put(per_user, user_id, hits)
  end
end
