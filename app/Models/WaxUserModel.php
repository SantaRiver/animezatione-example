<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\Relations\HasMany;
use Illuminate\Database\Eloquent\Relations\HasOne;
use Illuminate\Support\Facades\Http;

/**
 * Class WaxUserModel
 * @package App\Models
 */
class WaxUserModel extends Model
{
    use HasFactory;

    /**
     * @var string
     */
    protected $table = 'wax_users';
    /**
     * @var string[]
     */
    protected $fillable = [
        'userAccount',
        'pubKey',
        'secureKey',
        'permission',
    ];

    /**
     * Endpoint
     * @return HasMany
     */
    public function inventory(): HasMany
    {
        return $this->hasMany(UserInventory::class, 'user_id');
    }

    /**
     * @return HasOne
     */
    public function poolRattitude(): HasOne
    {
        return $this->hasOne(UserPoolRattitude::class, 'user_id');
    }

    /**
     * @return HasOne
     */
    public function wallet(): HasOne
    {
        return $this->hasOne(UserWallet::class, 'user_id');
    }

    /**
     * @return HasOne
     */
    public function stakingRate(): HasOne
    {
        return $this->hasOne(UserStakingRate::class, 'user_id');
    }

    /**
     * @return HasMany
     */
    public function transactions(): HasMany
    {
        return $this->hasMany(UserTransactions::class, 'user_id');
    }



    public function updateInventory() : void
    {
        $userWallet = $this->wallet()->first();
        if (empty($userWallet)) {
            $userWallet = new UserWallet(['user_id' => $this->id]);
        }
        $params = [
            'collection_name' => 'animezatione',
            'owner' => $this->userAccount,
            'limit' => 1500,
        ];
        $assetsUrl = 'https://wax.api.atomicassets.io/atomicassets/v1/assets?'.http_build_query($params);
        $assetsResponse = Http::get($assetsUrl)->json();
        if ($assetsResponse['success']) {
            $assets = $assetsResponse['data'];
            $existCardAssetsId = [];
            foreach ($assets as $asset) {
                 $this->inventory()->updateOrCreate(
                    [
                        'user_id' => $this->id,
                        'card_id' => NFTsModel::query()->firstWhere('template_id', $asset['template']['template_id'])->id,
                        'asset_id' => $asset['asset_id'],
                        'mint' => $asset['template_mint'] ?? null,
                    ]
                );
                $existCardAssetsId[] = $asset['asset_id'];
            }

            /**
             * Удаление записи, если карточки больше нет
             */
            UserInventory::query()
                ->where('user_id', $this->id)
                ->whereNotIn('asset_id', $existCardAssetsId)
                ->delete();


            $userPoolRattitude = UserPoolRattitude::query()->firstWhere('user_id', $this->id);
            if (empty($userPoolRattitude)) {
                $userPoolRattitude = new UserPoolRattitude(['user_id' => $this->id]);
            }

            /**
             * Расчет стейкинг рейта для каждого пользователя
             */
            $userStakingRate = UserStakingRate::query()->firstWhere('user_id', $this->id);
            if (empty($userStakingRate)) {
                $userStakingRate = new UserStakingRate(['user_id' => $this->id]);
            }
            $userInventory = UserInventory::query()
                ->where('user_id', $this->id)
                ->join('nft', 'users_inventory.card_id', '=', 'nft.id')
                ->get();

            $StakingRateAni = $userInventory->sum('benefit');
            $totalAni = UserWallet::query()->sum('ani');

            $userStakingRate->ani = $StakingRateAni;
            $userPoolRattitude->percent = ($userWallet->ani / $totalAni) * 100;

            $userPoolRattitude->save();
            $userWallet->save();
            $userStakingRate->save();
        } elseif ($assetsResponse['message'] == 'Rate limit') {
            sleep(5);
        }
    }

}
