<?php

namespace Tests\Feature;

use App\Models\PlayerRanking;
use App\Models\User;
use App\Services\PlayerAvatarService;
use Illuminate\Foundation\Testing\RefreshDatabase;
use Illuminate\Http\UploadedFile;
use Illuminate\Support\Facades\Storage;
use Tests\TestCase;

class MinecraftSkinAndProfileTest extends TestCase
{
    use RefreshDatabase;

    protected function setUp(): void
    {
        parent::setUp();
        Storage::fake('public');
    }

    /**
     * Create a synthetic 64x64 or custom dimension PNG for testing.
     */
    protected function createPng(int $width, int $height): UploadedFile
    {
        $rawScanlines = '';
        for ($y = 0; $y < $height; $y++) {
            $rawScanlines .= chr(0); // Filter 0
            for ($x = 0; $x < $width; $x++) {
                $rawScanlines .= chr(128) . chr(128) . chr(128) . chr(255);
            }
        }

        $packChunk = function (string $type, string $data): string {
            return pack('N', strlen($data)) . $type . $data . pack('N', crc32($type . $data));
        };

        $pngData = "\x89PNG\r\n\x1a\n"
            . $packChunk('IHDR', pack('NNCCCCC', $width, $height, 8, 6, 0, 0, 0))
            . $packChunk('IDAT', gzcompress($rawScanlines, 9))
            . $packChunk('IEND', '');

        $temp = tempnam(sys_get_temp_dir(), 'skin_test') . '.png';
        file_put_contents($temp, $pngData);

        return new UploadedFile($temp, 'steve_skin.png', 'image/png', null, true);
    }

    public function test_user_can_view_redesigned_profile_page(): void
    {
        $user = User::factory()->create([
            'minecraft_gamertag' => 'BedrockKing',
        ]);

        $response = $this->actingAs($user)->get('/profile');
        $response->assertStatus(200);
        $response->assertSee('BedrockKing');
        $response->assertSee('Minecraft Skin');
        $response->assertSee('Upload Minecraft Skin PNG');
    }

    public function test_valid_64x64_minecraft_skin_is_accepted_and_head_extracted(): void
    {
        $user = User::factory()->create([
            'minecraft_gamertag' => 'CustomPvpPlayer',
        ]);

        $validSkin = $this->createPng(64, 64);

        $response = $this->actingAs($user)->patch('/profile', [
            'name' => $user->name,
            'email' => $user->email,
            'minecraft_gamertag' => 'CustomPvpPlayer',
            'skin' => $validSkin,
        ]);

        $response->assertSessionHasNoErrors();
        $response->assertRedirect('/profile');

        $user->refresh();
        $this->assertNotNull($user->skin_path);
        $this->assertNotNull($user->avatar_head_path);
        Storage::disk('public')->assertExists($user->skin_path);
        Storage::disk('public')->assertExists($user->avatar_head_path);

        // Verify PlayerAvatarService resolves the custom uploaded head
        $avatarUrl = PlayerAvatarService::getAvatarUrl('CustomPvpPlayer', 64);
        $this->assertStringContainsString('avatars/head_', $avatarUrl);
    }

    public function test_invalid_dimension_skin_is_rejected(): void
    {
        $user = User::factory()->create();

        // 120x80 is NOT a valid Minecraft skin
        $invalidSkin = $this->createPng(120, 80);

        $response = $this->actingAs($user)->patch('/profile', [
            'name' => $user->name,
            'email' => $user->email,
            'minecraft_gamertag' => 'SomePlayer',
            'skin' => $invalidSkin,
        ]);

        $response->assertSessionHasErrors('skin');
        $user->refresh();
        $this->assertNull($user->skin_path);
        $this->assertNull($user->avatar_head_path);
    }

    public function test_user_can_remove_custom_skin(): void
    {
        $user = User::factory()->create([
            'skin_path' => 'skins/existing.png',
            'avatar_head_path' => 'avatars/existing_head.png',
        ]);
        Storage::disk('public')->put('skins/existing.png', 'test');
        Storage::disk('public')->put('avatars/existing_head.png', 'test');

        $response = $this->actingAs($user)->patch('/profile', [
            'name' => $user->name,
            'email' => $user->email,
            'remove_skin' => 1,
        ]);

        $response->assertSessionHasNoErrors();
        $user->refresh();
        $this->assertNull($user->skin_path);
        $this->assertNull($user->avatar_head_path);
        Storage::disk('public')->assertMissing('skins/existing.png');
        Storage::disk('public')->assertMissing('avatars/existing_head.png');
    }
}
